<h1>Créer un Nouveau Post</h1>

<form action="?c=Post&a=enregistrer" method="post">
    <div class="mb-3">
        <label for="titre" class="form-label">Titre</label>
        <input type="text" class="form-control" name="titre" id="titre" required>
    </div>

    <div class="mb-3">
        <label for="contenu" class="form-label">Contenu du message</label>
        <textarea class="form-control" name="contenu" id="contenu" rows="5" required></textarea>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Publier</button>
        <a href="?c=Post&a=lister" class="btn btn-secondary">Annuler</a>
    </div>
</form>