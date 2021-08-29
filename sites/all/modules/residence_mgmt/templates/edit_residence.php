<?php /*Accès depuis  :: https://ehpad.home/edit-residence/31459 , Quid Permissions ???
 action ::updateChambre*/?>
<section class="edit-residence">
    <div class="row">
        <div class="col-md-12">
            <div class="card mg-t-10 mg-b-10">
                <div class="card-header d-sm-flex align-items-start justify-content-between">
                    <h4 class="lh-5 mg-b-0">Modifier la résidence: <?php echo $chambre->title; ?></h4>
                </div>
<?php
if(isset($uuid) and $uuid){
    $x=Alptech\Wip\fun::sql("select body_value from field_data_body where entity_id=15920");
    echo $x[0]['body_value'];
    #$x=node_load(15920);
    $a=1;
    #print drupal_render(node_view(node_load(15920)));
/*
 * $fullFiche = node_load($nid);
 * 15920
 */
}
?>

                <div class="card-body pd-y-15 pd-x-10">

                    <form action="" method="POST">

                        <fieldset class="form-fieldset">
                            <legend>Gestion des tarifs chambres</legend>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="tarif_chambre_simple" class="d-block">Prix Min</label>
                                    <input type="number" step="0.01" min="0" lang="en" class="form-control" name="tarif_chambre_simple" id="tarif_chambre_simple" value="<?php echo $chambre->field_pr_prixmin['und'][0]['value']; ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="tarif_chambre_double" class="d-block">Prix F1</label>
                                    <input type="number" step="0.01" min="0" lang="en" class="form-control" name="tarif_chambre_double" id="tarif_chambre_double" value="<?php echo $chambre->field_pr_prixf1['und'][0]['value']; ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="tarif_chambre_simple_temporaire" class="d-block">Prix F1 Bis</label>
                                    <input type="number" step="0.01" min="0" lang="en" class="form-control" name="tarif_chambre_simple_temporaire" id="tarif_chambre_simple_temporaire" value="<?php echo $chambre->field_pr_prixf1bis['und'][0]['value']; ?>">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="tarif_chambre_double_temporaire" class="d-block">Prix F2</label>
                                    <input type="number" step="0.01" min="0" lang="en" class="form-control" name="tarif_chambre_double_temporaire" id="tarif_chambre_double_temporaire" value="<?php echo $chambre->field_pr_prixf2['und'][0]['value']; ?>">
                                </div>
                            </div>


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
