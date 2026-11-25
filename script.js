$(function () {
  // Live character counter (jQuery)
  $("#notes").on("input", function () { $("#count").text(this.value.length + "/200"); }).trigger("input");

  // Digits only in phone field
  $("#phone").on("input", function () { this.value = this.value.replace(/\D/g, ""); });

  // Client-side validation before submit
  function setErr(id, msg) {
    $('[data-for="' + id + '"]').text(msg);
    $("#" + id).toggleClass("invalid", !!msg);
    return !msg;
  }
  $("#appForm").on("submit", function (e) {
    var ok = true;
    ok = setErr("name",   $.trim($("#name").val()) ? "" : "Enter your full name.") && ok;
    ok = setErr("email",  /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($("#email").val()) ? "" : "Enter a valid email address.") && ok;
    ok = setErr("phone",  /^\d{10}$/.test($("#phone").val()) ? "" : "Phone number must be 10 digits.") && ok;
    ok = setErr("course", $("#course").val() ? "" : "Select a course.") && ok;
    if (!ok) { e.preventDefault(); $(".invalid").first().focus(); }
  });

  // Print button on the result card
  $("#printBtn").on("click", function () { window.print(); });
  // Bring the result into view
  if ($("#result").length) { $("html, body").animate({ scrollTop: $("#result").offset().top - 20 }, 300); }
});
