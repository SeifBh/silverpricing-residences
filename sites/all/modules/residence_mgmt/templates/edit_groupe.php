<section class="edit-residence">
    <div class="row">
        <div class="col-md-12">
            <div class="card mg-t-10 mg-b-10">
                <div class="card-header d-sm-flex align-items-start justify-content-between">
                    <h4 class="lh-5 mg-b-0">Modifier le groupe: <?php echo $groupe->name; ?></h4>
                </div>
                <div class="card-body pd-y-15 pd-x-10">

                    <form method="POST">

                        <fieldset class="form-fieldset mg-t-10">
                            <legend>Catégories de logement</legend>

                            <fieldset class="form-fieldset mg-t-10">
                                <legend>Logement studio</legend>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="cs_entree_de_gamme" class="d-block">Logement "entrée de gamme"</label>
                                        <input type="text" class="form-control" name="cs_entree_de_gamme" id="cs_entree_de_gamme" value="<?php echo (!empty($typesDeChambres->cs_entree_de_gamme)) ? $typesDeChambres->cs_entree_de_gamme : "entrée de gamme" ?>" />
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="cs_standard" class="d-block">Logement "F1"</label>
                                        <input type="text" class="form-control" name="cs_standard" id="cs_standard" value="<?php echo (!empty($typesDeChambres->cs_standard)) ? $typesDeChambres->cs_standard : "standard" ?>">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="cs_superieur" class="d-block">Logement "F1 bis"</label>
                                        <input type="text" class="form-control" name="cs_superieur" id="cs_superieur" value="<?php echo (!empty($typesDeChambres->cs_superieur)) ? $typesDeChambres->cs_superieur : "supérieur" ?>">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="cs_luxe" class="d-block">Logement "F2"</label>
                                        <input type="text" class="form-control" name="cs_luxe" id="cs_luxe" value="<?php echo (!empty($typesDeChambres->cs_luxe)) ? $typesDeChambres->cs_luxe : "luxe" ?>">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="cs_alzheimer" class="d-block">Logement "F3"</label>
                                        <input type="text"  class="form-control" name="cs_alzheimer" id="cs_alzheimer" value="<?php echo (!empty($typesDeChambres->cs_alzheimer)) ? $typesDeChambres->cs_alzheimer : "alzheimer" ?>">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="cs_aide_sociale" class="d-block">Logement "aide sociale"</label>
                                        <input type="text" class="form-control" name="cs_aide_sociale" id="cs_aide_sociale" value="<?php echo (!empty($typesDeChambres->cs_aide_sociale)) ? $typesDeChambres->cs_aide_sociale : "aide sociale" ?>">
                                    </div>
                                </div>

                            </fieldset>

                       
                        </fieldset>

                        <fieldset class="form-fieldset mg-t-10">
                            <div class="form-row">
                                <button type="submit" class="btn btn-primary pd-x-50">Enregistrer</button>
                            </div>
                        </fieldset>

                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
