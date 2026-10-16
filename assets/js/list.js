function tampilkan(id) {
  const element = document.getElementById(id);

  if (!element) {
    console.log("ID tidak ditemukan:", id);
    return;
  }

  element.style.display =
    element.style.display === "none" || element.style.display === ""
      ? "block"
      : "none";
}