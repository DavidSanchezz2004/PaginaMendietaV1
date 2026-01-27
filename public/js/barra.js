document.addEventListener("DOMContentLoaded", () => {
    // Exponer funciones globales para onclick=""
    window.openSidebar = function () {
        document.body.classList.add("sidebar-open");
    };

    window.closeSidebar = function () {
        document.body.classList.remove("sidebar-open");
    };

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") window.closeSidebar();
    });

    // Dropdown groups
    const groups = document.querySelectorAll(".group");

    function closeOtherGroups(exceptEl) {
        groups.forEach((g) => {
            if (!exceptEl || g !== exceptEl) g.classList.remove("open");
        });
    }

    document.querySelectorAll('.item[data-type="group"]').forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            const group = btn.closest(".group");
            const willOpen = !group.classList.contains("open");
            closeOtherGroups(group);
            group.classList.toggle("open", willOpen);
        });
    });

    console.log("barra.js OK"); // <-- para confirmar
});
