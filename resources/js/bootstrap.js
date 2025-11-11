const tokenMeta = document.head?.querySelector('meta[name="csrf-token"]');

if (tokenMeta) {
    window.csrfToken = tokenMeta.content;
}
