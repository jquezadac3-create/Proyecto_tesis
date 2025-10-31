function jornadaPurchase(e) {
    const detail = e.detail;

    console.log(detail);
}

function abonoPurchase(e) {
    const detail = e.detail;

    console.log(detail);
}

document.addEventListener('complete-jornada-purchase', jornadaPurchase);
document.addEventListener('complete-abono-purchase', abonoPurchase);