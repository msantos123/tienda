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
  /**
   * Agrega un producto al carrito.
   * @param {Object} product - El objeto producto completo
   * @param {number} quantity - Cantidad a agregar
   * @param {Object} buyerChoices - Selecciones del comprador {attrName: value} ej. { "Talla": "L", "Color": "Rojo" }
   */
  const addToCart = (product, quantity = 1, buyerChoices = {}) => {
    // Para productos con opciones (buyer_choices), cada combinación es un ítem distinto.
    // Para productos sin opciones, se acumula en el mismo ítem.
    const hasChoices = Object.keys(buyerChoices).length > 0

    if (!hasChoices) {
      const existingItem = cartItems.value.find(
        item => item.product.id === product.id && Object.keys(item.buyer_choices || {}).length === 0
      )
      if (existingItem) {
        existingItem.quantity += quantity
        if (existingItem.quantity > product.stock) {
          existingItem.quantity = product.stock
        }
        return
      }
    }

    cartItems.value.push({ product, quantity, buyer_choices: buyerChoices })
  }

  const removeFromCart = (index) => {
    cartItems.value.splice(index, 1)
  }

  const updateQuantity = (index, newQuantity) => {
    const item = cartItems.value[index]
    if (!item) return
    if (newQuantity <= 0) {
      removeFromCart(index)
    } else {
      item.quantity = Math.min(newQuantity, item.product.stock)
    }
  }

  const clearCart = () => {
    cartItems.value = []
  }

  const totalItemsCount = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.quantity, 0)
  })

  /**
   * Genera el mensaje de WhatsApp con todos los ítems del carrito,
   * incluyendo las selecciones del comprador (talla, color, etc.)
   */
  const generateWhatsAppMessage = () => {
    const itemsWithPrice    = cartItems.value.filter(i => i.product.show_price)
    const itemsWithoutPrice = cartItems.value.filter(i => !i.product.show_price)

    let msg = '🛒 *Hola, me gustaría hacer un pedido:*\n\n'

    const formatChoices = (choices) => {
      if (!choices || Object.keys(choices).length === 0) return ''
      const parts = Object.entries(choices).map(([k, v]) => `${k}: *${v}*`)
      return '\n   ↳ ' + parts.join(', ')
    }

    const formatSpecs = (product) => {
      if (product.attribute_values && product.attribute_values.length > 0) {
        const specs = product.attribute_values
          .map(av => `${av.attribute?.name || 'Especificación'}: ${av.value}`)
          .join(', ')
        return ` [${specs}]`
      }
      return ''
    }

    if (itemsWithPrice.length > 0) {
      msg += '*📦 Productos:*\n'
      let total = 0
      itemsWithPrice.forEach(item => {
        const price    = Number(item.product.sale_price || item.product.price)
        const subtotal = price * item.quantity
        total += subtotal
        msg += `• ${item.quantity}x *${item.product.name}*${formatSpecs(item.product)}`
        msg += formatChoices(item.buyer_choices)
        msg += ` — Bs. ${subtotal.toFixed(2)}\n`
      })
      msg += `\n*TOTAL: Bs. ${total.toFixed(2)}*\n\n`
    }

    if (itemsWithoutPrice.length > 0) {
      msg += '*🔖 Productos a cotizar:*\n'
      itemsWithoutPrice.forEach(item => {
        msg += `• ${item.quantity}x *${item.product.name}*${formatSpecs(item.product)}`
        msg += formatChoices(item.buyer_choices)
        msg += '\n'
      })
      msg += '\n¿Cuál sería el precio de estos productos?\n'
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
    generateWhatsAppMessage,
  }
}
