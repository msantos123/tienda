<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\WhatsappLead;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WhatsappLeadController extends Controller
{
    /**
     * Display a listing of WhatsApp Leads (Mini-CRM).
     */
    public function index(Request $request)
    {
        $query = WhatsappLead::with('product');

        // Búsqueda por referencia, cliente o teléfono
        if ($request->filled('search')) {
            $search = trim($request->search);
            
            // Automatización: Cambiar estado si se busca una referencia exacta y está en "nuevo"
            if (preg_match('/^REF-\d{4}$/i', $search)) {
                $exactLead = WhatsappLead::where('reference', 'ilike', $search)->first();
                if ($exactLead && $exactLead->current_status === 'nuevo') {
                    $exactLead->update(['current_status' => 'en_conversacion']);
                }
            }

            $query->where(function ($q) use ($search) {
                $q->where('reference', 'ilike', "%{$search}%")
                  ->orWhere('customer_name', 'ilike', "%{$search}%")
                  ->orWhere('phone_number', 'ilike', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        // Paginación simple
        $leads = $query->latest()->paginate(15)->withQueryString();

        // Agrupación por estados para saber el contador de cada pestaña
        $counts = WhatsappLead::selectRaw('current_status, count(*) as total')
            ->groupBy('current_status')
            ->pluck('total', 'current_status')
            ->toArray();

        // Asegurar que todos los estados tengan un valor
        $statuses = ['nuevo', 'en_conversacion', 'esperando_pago', 'pagado_pendiente', 'entregado', 'archivado'];
        $statusCounts = [];
        foreach ($statuses as $status) {
            $statusCounts[$status] = $counts[$status] ?? 0;
        }

        return Inertia::render('Tenant/WhatsappLeads/Index', [
            'leads' => $leads,
            'filters' => $request->only(['search', 'status']),
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * Public endpoint to create a lead when clicking "Comprar por WhatsApp".
     */
    public function storePublic(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'buyer_choices' => 'nullable|array',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        
        // Obtener el precio unitario
        $price = $product->sale_price ?? $product->price ?? 0;
        $totalAmount = $price * $validated['quantity'];

        // Crear Lead
        $lead = WhatsappLead::create([
            'product_id' => $product->id,
            'quantity' => $validated['quantity'],
            'buyer_choices' => $validated['buyer_choices'] ?? null,
            'current_status' => 'nuevo',
            'total_amount' => $totalAmount,
        ]);

        // Obtener el teléfono del administrador
        $adminPhone = User::first()?->phone;
        if (!$adminPhone) {
            return response()->json([
                'error' => 'La tienda no tiene configurado un número de WhatsApp de contacto.'
            ], 422);
        }

        $phone = preg_replace('/\D/', '', $adminPhone);

        // Construir mensaje
        $choicesText = '';
        if (!empty($lead->buyer_choices)) {
            $details = [];
            foreach ($lead->buyer_choices as $attrName => $value) {
                if (!empty($value)) {
                    $details[] = "$attrName: $value";
                }
            }
            if (count($details) > 0) {
                $choicesText = " - Opciones: (" . implode(', ', $details) . ")";
            }
        }

        $message = "Hola, me interesa el producto *{$product->name}* (Ref: {$lead->reference}){$choicesText}. ¿Tienen stock?";
        
        $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);

        return response()->json([
            'whatsapp_url' => $whatsappUrl,
            'reference' => $lead->reference,
        ]);
    }

    /**
     * Update lead details (name, phone, status, total_amount).
     */
    public function update(Request $request, WhatsappLead $lead)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:30',
            'total_amount' => 'required|numeric|min:0',
            'current_status' => 'required|in:nuevo,en_conversacion,esperando_pago,pagado_pendiente,entregado,archivado',
        ]);

        $lead->update($validated);

        return redirect()->back()->with('success', 'Lead actualizado correctamente.');
    }

    /**
     * Update only the status (useful for quick drag-drop or fast click changes).
     */
    public function updateStatus(Request $request, WhatsappLead $lead)
    {
        $validated = $request->validate([
            'status' => 'required|in:nuevo,en_conversacion,esperando_pago,pagado_pendiente,entregado,archivado',
        ]);

        $lead->update([
            'current_status' => $validated['status']
        ]);

        return redirect()->back()->with('success', 'Estado del lead actualizado.');
    }

    /**
     * Delete a lead.
     */
    public function destroy(WhatsappLead $lead)
    {
        $lead->delete();
        return redirect()->back()->with('success', 'Lead eliminado correctamente.');
    }
}
