document.addEventListener("submit", function (event) {
    const form = event.target;
    if (form.matches("[data-confirm]")) {
        const message = form.getAttribute("data-confirm") || "Are you sure?";
        if (!window.confirm(message)) event.preventDefault();
    }
});
