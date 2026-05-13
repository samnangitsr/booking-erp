import "../../css/app.css";
import { createInertiaApp } from "@inertiajs/react";
import { createRoot } from "react-dom/client";

// Minimal Inertia + React entry point. The admin UI is Blade-driven; this
// app boots the React-powered customer-facing portion of the site. Add more
// pages under resources/js/inertia/Pages/ as needed.
createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.jsx", { eager: true });
        return pages[`./Pages/${name}.jsx`];
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: "#4f46e5",
        showSpinner: true,
    },
});
