import Alpine from 'alpinejs';

/**
 * Thin fetch wrapper that adds the CSRF token + JSON headers and normalises
 * Laravel's responses. On a 422 it throws an error carrying `errors` so callers
 * can render inline validation messages (no alert()).
 */
async function apiFetch(url, { method = 'GET', body = null } = {}) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    const response = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: body ? JSON.stringify(body) : null,
    });

    const payload = await response.json().catch(() => ({}));

    if (response.status === 422) {
        const error = new Error('Validation failed');
        error.validation = payload.errors ?? {};
        throw error;
    }

    if (!response.ok) {
        throw new Error(payload.message ?? 'Request failed');
    }

    return payload;
}

// Expose globally for Alpine components.
window.apiFetch = apiFetch;

/**
 * Tags attach/detach on an issue. Keeps the tag list in sync with the server
 * via AJAX and never reloads the page.
 */
Alpine.data('issueTags', ({ issueId, tags, allTags }) => ({
    issueId,
    tags,
    allTags,
    selected: '',
    busy: false,
    error: '',

    // Tags not yet attached, available to add.
    get availableTags() {
        const attached = new Set(this.tags.map((t) => t.id));
        return this.allTags.filter((t) => !attached.has(t.id));
    },

    async attach() {
        if (!this.selected || this.busy) return;
        this.busy = true;
        this.error = '';
        try {
            const { data } = await apiFetch(`/issues/${this.issueId}/tags`, {
                method: 'POST',
                body: { tag_id: Number(this.selected) },
            });
            this.tags = data;
            this.selected = '';
        } catch (e) {
            this.error = e.validation?.tag_id?.[0] ?? e.message;
        } finally {
            this.busy = false;
        }
    },

    async detach(tag) {
        if (this.busy) return;
        this.busy = true;
        this.error = '';
        try {
            const { data } = await apiFetch(`/issues/${this.issueId}/tags/${tag.id}`, {
                method: 'DELETE',
            });
            this.tags = data;
        } catch (e) {
            this.error = e.message;
        } finally {
            this.busy = false;
        }
    },
}));

window.Alpine = Alpine;

Alpine.start();
