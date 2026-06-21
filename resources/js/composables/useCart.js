import { ref, computed, watch } from 'vue'

const cartItems = ref([])
const isCartOpen = ref(false)

// Cargar del localStorage
const savedCart = localStorage.getItem('tenant_cart')
if (savedCart) {
  try {
    cartItems.value = JSON.parse(savedCart)
  } catch (e) {
    console.error('Error parsing cart from local storage')
  }
}

// Guardar en localStorage cada vez que cambie
watch(cartItems, (newVal) => {
  localStorage.setItem('tenant_cart', JSON.stringify(newVal))
}, { deep: true })

export function useCart() {
  const addToCart = (product, quantity = 1) => {
    const existingItem = cartItems.value.find(item => item.product.id === product.id)
    if (existingItem) {
      existingItem.quantity += quantity
      if (existingItem.quantity > product.stock) {
        existingItem.quantity = product.stock // Limit to max stock
      }
    } else {
      cartItems.value.push({ product, quantity })
    }
  }

  const removeFromCart = (productId) => {
    cartItems.value = cartItems.value.filter(item => item.product.id !== productId)
  }

  const updateQuantity = (productId, newQuantity) => {
    const item = cartItems.value.find(i => i.product.id === productId)
    if (item) {
      if (newQuantity <= 0) {
        removeFromCart(productId)
      } else {
        item.quantity = newQuantity
        if (item.quantity > item.product.stock) {
          item.quantity = item.product.stock
        }
      }
    }
  }

  const clearCart = () => {
    cartItems.value = []
  }

  const totalItemsCount = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.quantity, 0)
  })

  // Generar el mensaje de WhatsApp según el requerimiento del usuario
  const generateWhatsAppMessage = () => {
    const itemsWithPrice = cartItems.value.filter(i => i.product.show_price)
    const itemsWithoutPrice = cartItems.value.filter(i => !i.product.show_price)
    
    let msg = "Hola, me gustaría hacer un pedido:\n\n"
    
    const formatSpecs = (product) => {
      if (product.attribute_values && product.attribute_values.length > 0) {
        const specs = product.attribute_values.map(av => `${av.attribute?.name || 'Especificación'}: ${av.value}`).join(', ')
        return ` [${specs}]`
      }
      return ''
    }

    if (itemsWithPrice.length > 0) {
      msg += "*PRODUCTOS CON PRECIO FIJADO:*\n"
      let total = 0
      itemsWithPrice.forEach(item => {
        const price = Number(item.product.sale_price || item.product.price)
        const subtotal = price * item.quantity
        total += subtotal
        msg += `- ${item.quantity}x ${item.product.name} (SKU: ${item.product.sku})${formatSpecs(item.product)} - $${subtotal.toFixed(2)}\n`
      })
      msg += `\n*TOTAL (Productos con precio): $${total.toFixed(2)}*\n\n`
    }
    
    if (itemsWithoutPrice.length > 0) {
      msg += "¿Y qué precio tienen estos productos?\n*Detalle de los productos:*\n"
      itemsWithoutPrice.forEach(item => {
        msg += `- ${item.quantity}x ${item.product.name} (SKU: ${item.product.sku})${formatSpecs(item.product)}\n`
      })
    }
    
    return encodeURIComponent(msg)
  }

  return {
    cartItems,
    isCartOpen,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    totalItemsCount,
    generateWhatsAppMessage
  }
}
