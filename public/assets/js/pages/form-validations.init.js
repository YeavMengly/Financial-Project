/*
Template Name: Minia - Admin & Dashboard Template
Author: Themesbrand
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Form validation Js File
*/

window.onload = function () {
    // pristinejs validation
    var form = document.getElementById("pristine-valid-example");
    var pristine = new Pristine(form);
    form.addEventListener("submit", function (e) {
        if (!pristine.validate()) {
            e.preventDefault();
        }
    });
};
