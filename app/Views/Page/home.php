<h1>DeustaGramme</h1>

<p>
    Vous voyez Instagram ? Bah c'est la même chose mais en moins bien, et pour une petite entreprise.<br/>
</p>

<hr>

<?php if (empty($posts)) : ?>
    <div class="alert alert-info">Aucun post n'a encore été publié. Soyez le premier !</div>
<?php endif; ?>

<div class="row">
    <?php foreach ($posts as $post) : ?>
    <div class="col-12 mb-4">
        <div class="card" data-post-id="<?php echo $post['id']; ?>">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <strong class="post-titre-editable" data-id="<?php echo $post['id']; ?>" <?php echo (isset($_SESSION['id']) && $_SESSION['id'] == $post['utilisateur_id']) ? 'contenteditable="true"' : ''; ?>>
                        <?php echo htmlspecialchars($post['titre']); ?>
                    </strong>
                </div>
                <div>
                    Publié par <strong><?php echo htmlspecialchars($post['nom_utilisateur']); ?></strong> le <?php echo date('d/m/Y H:i', strtotime($post['date_publication'])); ?>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text post-contenu-editable" data-id="<?php echo $post['id']; ?>" <?php echo (isset($_SESSION['id']) && $_SESSION['id'] == $post['utilisateur_id']) ? 'contenteditable="true"' : ''; ?>>
                    <?php echo nl2br(htmlspecialchars($post['contenu'])); ?>
                </p>
                
                <?php if (isset($_SESSION['id']) && $_SESSION['id'] == $post['utilisateur_id']) : ?>
                    <hr>
                    <a href="?c=Post&a=supprimer&id=<?php echo $post['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce post ?');">Supprimer</a>
                <?php endif; ?>

                <hr>

                <div class="d-flex justify-content-between align-items-center">
                    <?php if (isset($_SESSION['id'])) : ?>
                        <button class="btn btn-sm reaction-toggle" data-post-id="<?php echo $post['id']; ?>" data-liked="<?php echo $post['user_has_liked'] ? 'true' : 'false'; ?>">
                            <i class="bi bi-heart-fill <?php echo $post['user_has_liked'] ? 'text-danger' : 'text-secondary'; ?>"></i> J'aime
                        </button>
                    <?php endif; ?>
                    
                    <span class="text-muted small">
                        <span class="likes-count" data-post-id="<?php echo $post['id']; ?>"><?php echo $post['likes_count']; ?></span> J'aime
                    </span>
                </div>
                
                <hr>
                
                <h6 class="mb-3 text-muted" id="comment-count-<?php echo $post['id']; ?>">
                    <?php echo count($post['commentaires']); ?> commentaire(s)
                </h6> 

                <div class="comments-list" id="comments-list-<?php echo $post['id']; ?>">
                    <?php foreach ($post['commentaires'] as $comment) : ?>
                        <div class="card mb-2 p-2 bg-light comment-item" data-comment-id="<?php echo $comment['id']; ?>">
                            <p class="mb-0 small">
                                <strong><?php echo htmlspecialchars($comment['nom_utilisateur']); ?></strong>: <?php echo htmlspecialchars($comment['contenu']); ?>
                            </p>
                            <span class="text-muted small">
                                <?php echo date('d/m/Y H:i', strtotime($comment['date_commentaire'])); ?>
                                <?php if (isset($_SESSION['id']) && $_SESSION['id'] == $comment['utilisateur_id']) : ?>
                                    - <a href="?c=Commentaires&a=supprimer&id=<?php echo $comment['id']; ?>" class="text-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">Supprimer</a>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (isset($_SESSION['id'])) : ?>
                    <div class="mt-3 add-comment-form">
                        <form class="form-add-comment" data-post-id="<?php echo $post['id']; ?>">
                            <div class="input-group">
                                <textarea name="contenu" class="form-control form-control-sm" placeholder="Ajouter un commentaire..." rows="1" required></textarea>
                                <button type="submit" class="btn btn-primary btn-sm">Commenter</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (isset($_SESSION['id'])) : ?>
<script>
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

    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
<?php endif; ?>