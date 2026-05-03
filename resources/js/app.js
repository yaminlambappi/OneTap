import './bootstrap';

// ── Reaction toggle ────────────────────────────────────────────────────────────
window.toggleReaction = async function(type, id, reactionType) {
    try {
        const res = await fetch('/reactions/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
            },
            body: JSON.stringify({
                reactable_type: type,
                reactable_id:   id,
                type:           reactionType,
            }),
        });
        const data = await res.json();

        // Update all reaction counts for this item
        const card = document.querySelector(`[data-id="${id}"][data-type="${type}"]`);
        if (card && data.counts) {
            Object.entries(data.counts).forEach(([rType, count]) => {
                const countEl = card.querySelector(`[data-reaction="${rType}"]`);
                if (countEl) countEl.textContent = count > 0 ? count : '';
            });
        }
    } catch(e) {
        console.error('Reaction error:', e);
    }
};

// Listen for global reactions
if (typeof window !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        if (window.Echo) {
            const userCampusId = window.userCampusId || 'global';
            const channel = userCampusId !== 'global' ? `reactions.campus.${userCampusId}` : 'reactions.global';
            
            window.Echo.channel(channel)
                .listen('.reaction.updated', (data) => {
                    const shortType = data.reactable_type.split('\\').pop().toLowerCase();
                    const card = document.querySelector(`[data-id="${data.reactable_id}"][data-type="${shortType}"]`);
                    if (card && data.counts) {
                        Object.entries(data.counts).forEach(([rType, count]) => {
                            const countEl = card.querySelector(`[data-reaction="${rType}"]`);
                            if (countEl) countEl.textContent = count > 0 ? count : '';
                        });
                    }
                });
        }
    });
}

// ── Comment toggle ─────────────────────────────────────────────────────────────
window.toggleComments = async function(id, type) {
    const section = document.getElementById(`comments-${id}-${type}`);
    if (!section) return;

    const isHidden = section.classList.contains('hidden');
    section.classList.toggle('hidden', !isHidden);

    if (isHidden) {
        // Load comments
        try {
            const res = await fetch(`/comments?commentable_type=${type}&commentable_id=${id}`);
            // For now just show the input — full comment loading can be added
        } catch(e) {}
    }
};

// ── Submit comment ─────────────────────────────────────────────────────────────
window.submitComment = async function(input, id, type) {
    const body = input.value.trim();
    if (!body) return;

    const anonToggle = input.closest('.flex').querySelector('.anon-comment-toggle');
    const isAnon = anonToggle?.checked ?? false;

    try {
        const res = await fetch('/comments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
            },
            body: JSON.stringify({
                commentable_type: type,
                commentable_id:   id,
                body,
                is_anonymous: isAnon,
            }),
        });
        const comment = await res.json();

        if (comment.id) {
            const list = document.getElementById(`comments-list-${id}-${type}`);
            if (list) {
                list.insertAdjacentHTML('beforeend', buildCommentHTML(comment));
            }
            input.value = '';
        }
    } catch(e) {
        console.error('Comment error:', e);
    }
};

function buildCommentHTML(comment) {
    return `
    <div class="flex items-start gap-2">
        <img src="${comment.author_avatar}" class="w-6 h-6 rounded-full flex-shrink-0 mt-0.5 object-cover">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-0.5">
                <span class="text-xs font-semibold text-zinc-300">${comment.author_name}</span>
                ${comment.is_anonymous ? '<span class="text-xs text-fuchsia-400">anon</span>' : ''}
                <span class="text-xs text-zinc-600">${comment.created_at}</span>
            </div>
            <p class="text-sm text-zinc-300">${comment.body}</p>
        </div>
    </div>`;
}

// ── Report content ─────────────────────────────────────────────────────────────
window.reportContent = function(type, id) {
    const reason = prompt('Reason: spam, harassment, nsfw, misinformation, other');
    if (!reason) return;

    fetch('/reports', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken,
        },
        body: JSON.stringify({
            reportable_type: type,
            reportable_id:   id,
            reason,
        }),
    }).then(() => alert('Reported. Thanks for keeping the community safe.'));
};

// ── Poll voting ────────────────────────────────────────────────────────────────
window.votePoll = async function(pollId, optionId) {
    try {
        const res = await fetch(`/polls/${pollId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
            },
            body: JSON.stringify({ option_id: optionId }),
        });
        const data = await res.json();

        if (data.error) {
            alert(data.error);
            return;
        }

        // Update poll UI
        const pollEl = document.getElementById(`poll-${pollId}`);
        if (pollEl && data.options) {
            data.options.forEach(opt => {
                const btn = pollEl.querySelector(`[data-option="${opt.id}"]`);
                if (btn) {
                    const bar = btn.querySelector('div:first-child');
                    if (bar) bar.style.width = opt.percentage + '%';
                    const pctEl = btn.querySelector('.relative span:last-child');
                    if (pctEl) pctEl.textContent = opt.percentage + '%';
                }
            });
            const votesEl = document.getElementById(`poll-votes-${pollId}`);
            if (votesEl) votesEl.textContent = data.total_votes + ' votes';
        }
    } catch(e) {
        console.error('Poll vote error:', e);
    }
};
