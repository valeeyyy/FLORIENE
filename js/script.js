function scrollToOrder(productId) {
  document.getElementById("pesan").scrollIntoView({ behavior: "smooth" });
}

document.addEventListener("DOMContentLoaded", function () {
  var tglEl = document.getElementById("f-tgl");
  if (tglEl) {
    tglEl.min = new Date().toISOString().split("T")[0];
  }
});
