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

/**
 * Comments for an issue: paginated "load more" plus AJAX create that prepends
 * the new comment and clears the form. Comment markup is rendered server-side
 * (shared Blade partial) and injected as HTML, so there is no duplicated template.
 */
Alpine.data('issueComments', ({ issueId }) => ({
    issueId,
    items: [],
    page: 0,
    hasMore: false,
    total: 0,
    loading: false,
    busy: false,
    form: { author_name: '', body: '' },
    errors: {},

    async load() {
        if (this.loading) return;
        this.loading = true;
        try {
            const next = this.page + 1;
            const { html, meta } = await apiFetch(`/issues/${this.issueId}/comments?page=${next}`);
            this.items.push(...html);
            this.page = meta.current_page;
            this.hasMore = meta.has_more;
            this.total = meta.total;
        } catch (e) {
            // Leave the list as-is on failure; nothing destructive happened.
        } finally {
            this.loading = false;
        }
    },

    async submit() {
        if (this.busy) return;
        this.busy = true;
        this.errors = {};
        try {
            const { html, total } = await apiFetch(`/issues/${this.issueId}/comments`, {
                method: 'POST',
                body: { ...this.form },
            });
            this.items.unshift(html);   // prepend newest
            this.total = total;
            this.form = { author_name: '', body: '' };   // clear the form
        } catch (e) {
            this.errors = e.validation ?? {};
        } finally {
            this.busy = false;
        }
    },
}));

/**
 * Member assignment on an issue (bonus). Mirrors issueTags: attach/detach
 * users via AJAX, keep the list in sync, no page reload.
 */
Alpine.data('issueMembers', ({ issueId, members, allUsers }) => ({
    issueId,
    members,
    allUsers,
    selected: '',
    busy: false,
    error: '',

    get availableUsers() {
        const assigned = new Set(this.members.map((m) => m.id));
        return this.allUsers.filter((u) => !assigned.has(u.id));
    },

    async attach() {
        if (!this.selected || this.busy) return;
        this.busy = true;
        this.error = '';
        try {
            const { data } = await apiFetch(`/issues/${this.issueId}/members`, {
                method: 'POST',
                body: { user_id: Number(this.selected) },
            });
            this.members = data;
            this.selected = '';
        } catch (e) {
            this.error = e.validation?.user_id?.[0] ?? e.message;
        } finally {
            this.busy = false;
        }
    },

    async detach(member) {
        if (this.busy) return;
        this.busy = true;
        this.error = '';
        try {
            const { data } = await apiFetch(`/issues/${this.issueId}/members/${member.id}`, {
                method: 'DELETE',
            });
            this.members = data;
        } catch (e) {
            this.error = e.message;
        } finally {
            this.busy = false;
        }
    },
}));

window.Alpine = Alpine;

Alpine.start();
