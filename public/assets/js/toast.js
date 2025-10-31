var toast = {
    show: (variant, title, message, list, sound) => document.dispatchEvent(new CustomEvent('notify', { detail: { variant, title, message, list, sound } }))
}