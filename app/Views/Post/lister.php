<h1>Fil d'actualité</h1>

<?php if (empty($posts)) : ?>
    <div class="alert alert-info">Aucun message n'a encore été publié. Soyez le premier !</div>
<?php endif; ?>

<div class="row">
    <?php foreach ($posts as $post) : ?>
    <div class="col-12 mb-4">
        <div class="card" data-post-id="<?php echo $post['id']; ?>">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <strong><?php echo htmlspecialchars($post['titre']); ?></strong> 
                </div>
                <div>
                    Publié par <strong><?php echo htmlspecialchars($post['nom_utilisateur']); ?></strong> le <?php echo date('d/m/Y H:i', strtotime($post['date_publication'])); ?>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text"><?php echo nl2br(htmlspecialchars($post['contenu'])); ?></p>
                
                <?php if (isset($_SESSION['id']) && $_SESSION['id'] == $post['utilisateur_id']) : ?>
                    <hr>
                    <a href="?c=Post&a=modifier&id=<?php echo $post['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                    <a href="?c=Post&a=supprimer&id=<?php echo $post['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce post ?');">Supprimer</a>
                <?php endif; ?>

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
    document.addEventListener('DOMContentLoaded', function() {
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
                    alert('Une erreur s\'est produite lors de la communication avec le serveur.');
                });
            });
        });
    });
</script>
<?php endif; ?>