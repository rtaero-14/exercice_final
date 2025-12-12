document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const resultsContainer = document.getElementById('search-results-container');
    let searchTimeout;
    
    let lastKnownPostId = (typeof posts !== 'undefined' && posts.length > 0) ? posts[0].id : 0;
    const notificationBadge = document.getElementById('notification-badge');

    function displayResults(results) {
        resultsContainer.innerHTML = '';
        
        let html = '';
        const allResults = [].concat(results.posts, results.comments, results.users);

        if (allResults.length === 0) {
            resultsContainer.innerHTML = '<p class="text-muted mb-0">Aucun résultat trouvé.</p>';
            resultsContainer.style.display = 'block';
            return;
        }

        if (results.posts.length > 0) {
            html += '<h6>Posts (' + results.posts.length + ')</h6>';
            results.posts.slice(0, 3).forEach(item => {
                html += `<div class="p-1 border-bottom small"><strong>${item.titre}</strong> par ${item.nom_utilisateur}</div>`;
            });
        }
        if (results.comments.length > 0) {
            html += '<h6 class="mt-2">Commentaires (' + results.comments.length + ')</h6>';
             results.comments.slice(0, 3).forEach(item => {
                html += `<div class="p-1 border-bottom small">💬 ${item.contenu.substring(0, 30)}... (sur ${item.titre_post})</div>`;
            });
        }
        if (results.users.length > 0) {
            html += '<h6 class="mt-2">Utilisateurs (' + results.users.length + ')</h6>';
             results.users.slice(0, 3).forEach(item => {
                html += `<div class="p-1 small">👤 ${item.nom}</div>`;
            });
        }

        resultsContainer.innerHTML = html;
        resultsContainer.style.display = 'block';
    }

    function performSearch() {
        const query = searchInput.value.trim();
        if (query.length < 2) {
            resultsContainer.style.display = 'none';
            return;
        }

        fetch(`?c=Search&a=ajaxSearch&q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            displayResults(data.results);
        })
        .catch(error => {
            console.error('Erreur de recherche Ajax:', error);
            resultsContainer.innerHTML = '<p class="text-danger mb-0">Erreur de connexion au serveur.</p>';
            resultsContainer.style.display = 'block';
        });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 300);
    });

    document.addEventListener('click', function(e) {
        if (!resultsContainer.contains(e.target) && e.target !== searchInput) {
            resultsContainer.style.display = 'none';
        }
    });
    
    searchInput.addEventListener('focus', function() {
        if (searchInput.value.length >= 2 && resultsContainer.innerHTML !== '') {
            resultsContainer.style.display = 'block';
        }
    });

    function checkNotifications() {
        if (!notificationBadge) return;
        
        fetch('?c=Notification&a=checkNewActivities')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.latest_post_id > lastKnownPostId) {
                notificationBadge.textContent = 'Nouveau!';
                notificationBadge.classList.remove('d-none');
                lastKnownPostId = data.latest_post_id; 
            }
        })
        .catch(error => {
            console.error('Erreur vérification notifications:', error);
        });
    }

    if (notificationBadge) {
        setInterval(checkNotifications, 10000); 
        
        notificationBadge.parentElement.addEventListener('click', function() {
             notificationBadge.classList.add('d-none');
        });
    }

    const commentForms = document.querySelectorAll('.form-add-comment');

    commentForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const postId = this.getAttribute('data-post-id');
            const contentInput = this.querySelector('textarea[name="contenu"]');
            const content = contentInput.value.trim();

            if (content.length === 0) {
                alert('Le commentaire ne peut pas être vide.');
                return;
            }

            const formData = new FormData();
            formData.append('contenu', content);
            formData.append('post_id', postId);

            fetch('?c=Commentaires&a=ajouter', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    contentInput.value = '';
                    
                    const commentHtml = `
                        <div class="card mb-2 p-2 bg-light comment-item" data-comment-id="${data.comment.id}">
                            <p class="mb-0 small">
                                <strong>${data.comment.nom_utilisateur}</strong>: ${data.comment.contenu}
                            </p>
                            <span class="text-muted small">
                                ${new Date(data.comment.date_commentaire).toLocaleDateString()} ${new Date(data.comment.date_commentaire).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                - <a href="?c=Commentaires&a=supprimer&id=${data.comment.id}" class="text-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">Supprimer</a>
                            </span>
                        </div>
                    `;
                    
                    const commentsList = document.getElementById('comments-list-' + postId);
                    commentsList.insertAdjacentHTML('beforeend', commentHtml);
                    
                    const commentCountElement = document.getElementById('comment-count-' + postId);
                    let currentCount = parseInt(commentCountElement.textContent.match(/\d+/)[0]);
                    commentCountElement.textContent = (currentCount + 1) + ' commentaire(s)';
                    
                } else {
                    alert('Erreur lors de l\'ajout du commentaire: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur de la requête Fetch:', error);
                alert('Une erreur s\'est produite lors de la communication avec le serveur (Comment).');
            });
        });
    });
    
    const reactionButtons = document.querySelectorAll('.reaction-toggle');
    
    reactionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const isLiked = this.getAttribute('data-liked') === 'true';
            const icon = this.querySelector('i');
            const countSpan = document.querySelector(`.likes-count[data-post-id="${postId}"]`);
            
            const formData = new FormData();
            formData.append('post_id', postId);

            fetch('?c=Reaction&a=toggle', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'liked') {
                        button.setAttribute('data-liked', 'true');
                        icon.classList.remove('text-secondary');
                        icon.classList.add('text-danger');
                    } else {
                        button.setAttribute('data-liked', 'false');
                        icon.classList.remove('text-danger');
                        icon.classList.add('text-secondary');
                    }
                    
                    countSpan.textContent = data.new_count;
                    
                } else {
                    alert('Erreur lors de la gestion de la réaction: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur de la requête Fetch réaction:', error);
                alert('Une erreur s\'est produite lors de la communication avec le serveur (Réaction).');
            });
        });
    });

    function updatePostAjax(element, type) {
        const postId = element.getAttribute('data-id');
        
        const card = element.closest('.card');
        const titreElement = card.querySelector('.post-titre-editable');
        const contenuElement = card.querySelector('.post-contenu-editable');

        const titre = titreElement.textContent.trim();
        const contenu = contenuElement.textContent.trim();

        const initialTitre = titreElement.getAttribute('data-initial-titre') || titre;
        const initialContenu = contenuElement.getAttribute('data-initial-contenu') || contenu;

        if (titre === initialTitre && contenu === initialContenu) {
            return; 
        }

        const formData = new FormData();
        formData.append('id', postId);
        formData.append('titre', titre);
        formData.append('contenu', contenu);

        fetch('?c=Post&a=update', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok || response.status === 302) {
                alert("Post modifié avec succès (rechargement requis pour voir le message de session).");
            } else {
                 alert("Erreur lors de la mise à jour du post.");
            }
        })
        .catch(error => {
            console.error('Erreur AJAX post update:', error);
            alert('Une erreur s\'est produite lors de la communication avec le serveur (Post Update).');
        });
    }

    const editableElements = document.querySelectorAll('.post-titre-editable[contenteditable="true"], .post-contenu-editable[contenteditable="true"]');
    
    editableElements.forEach(element => {
        if (element.classList.contains('post-titre-editable')) {
            element.setAttribute('data-initial-titre', element.textContent.trim());
        } else if (element.classList.contains('post-contenu-editable')) {
             element.setAttribute('data-initial-contenu', element.textContent.trim());
        }
        
        element.addEventListener('blur', function() {
            const type = element.classList.contains('post-titre-editable') ? 'titre' : 'contenu';
            updatePostAjax(element, type);
        });
        
        element.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey && element.classList.contains('post-titre-editable')) {
                e.preventDefault(); 
                element.blur(); 
            }
        });
    });
});