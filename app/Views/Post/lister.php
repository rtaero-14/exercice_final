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