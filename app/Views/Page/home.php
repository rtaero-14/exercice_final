<h1>Ceci est un réseau social !</h1>

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
        <div class="card">
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

                <p class="mt-3 text-muted">0 commentaires</p> 
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>