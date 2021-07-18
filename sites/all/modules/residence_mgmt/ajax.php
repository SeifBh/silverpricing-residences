<?php
#https://ehpad.home/ajax/get-evolution-menusuelle-des-tarifs/40233
#cuj http://ehpad.silverpricing.fr/ajax/get-evolution-menusuelle-des-tarifs/33007 a {} 1
function residence_mgmt_get_evolution_menusuelle_des_tarifs($residenceId = null,$limit=10)
{
    if ($residenceId == null) {
        return;
    }
    $residenceNid = $residenceId;
    $residence = entity_load('node', array($residenceId), array('type' => 'residence'));
    $sameStatut = $residence[$residenceId]->field_type['und'][0]['value'];
    $clo = $_closestResidences = getResidencesProchesByStatus($residenceNid, [$sameStatut], $limit, 1);#nb:by status !!!
    $k0 = array_slice($_closestResidences, 0, 10);
#array_slice(,0,10)

    $as = $_t3 = [];
    $tarifss_1 = 0;
    $__sup = 'direct-withStatus';
    $prochesByStatus = getResidencesProchesByStatus(compact('residenceNid', 'clo', 'limit', '__sup'));
    foreach ($prochesByStatus as $t) {
        $_t3[$t->nid] = $t->field_pr_prixmin_value;
    }
    ksort($_t3);
    $as[3] = array_sum($_t3);

    if (isset($_SERVER['WINDIR'])) {#
        $tarifsA = [33089 => '{"33171":"98.5","33121":"82.3","33133":"81.72","33169":"98.2","33135":"82.1","33087":"55.57","33147":"84.28","33151":"87.5","33119":"79.83", "33127":"80.86"}'];#
        if (isset($tarifsA[$residenceId])) {
            $_tarifss_1 = json_decode($tarifsA[$residenceId], 1);
            $as[1] = array_sum($_tarifss_1);#
        }
    }


    $chartData = ['xAxe' => [], 'dataResidence' => [], 'dataDepartement' => [], 'dataResidencesConcurrents' => [],];
    $query = db_select('field_data_field_residence_id', 'rc');
    $query->condition('rc.field_residence_id_value', $residenceId, '=');
    $query->fields('rc', array('entity_id'));
    $chambreCourant = fetchObject($query);

    for ($i = 0; $i < 24; $i++) {
        $chartData['xAxe'][] = date('Y-m', strtotime(date('Y-m-d') . "- $i month"));
    }#les 24 derniers mois
    $chartData['xAxe'] = array_reverse($chartData['xAxe']);

    // RESIDENCE -- matche les prix avec la résidence
    /*
    $query = db_select('field_revision_field_date_de_modification', 'dm');
    $query->leftJoin('field_revision_field_tarif_chambre_simple', 'cs', 'cs.revision_id = dm.revision_id', array());
    $query->condition('dm.entity_id', $chambreCourant->entity_id, '=');
    */
    $query = db_select('field_revision_field_pr_prixmin', 'cs');
    $query->condition('cs.entity_id', $chambreCourant->entity_id, '=');
    $query->join('node_revision', 'nr', 'cs.revision_id = nr.vid', []);#$query->addExpression("DATE_FORMAT(FROM_UNIXTIME(nr.timestamp), '%Y-%m')", 'date_modification');
    //$query->addExpression("COUNT(dm.entity_id)", 'nombre_revision');
    //$query->addExpression("SUM(CAST(cs.field_tarif_chambre_simple_value AS DECIMAL(8,2)))", 'tarif_chambre');
    $query->fields('cs', ['entity_id', 'field_pr_prixmin_value']);
    $query->addExpression("DATE_FORMAT(FROM_UNIXTIME(nr.timestamp), '%Y-%m')", 'date_modification');
    $query->groupBy('date_modification');#si-si !!
    $query->orderBy('date_modification', 'desc');#last one from it
    $chambres = fetchAll($query);

    $tarif = [];
    foreach ($chambres as $chambre) {
        $dm = $chambre->date_modification;
        $tarif[$dm] = $chambre->field_pr_prixmin_value;
    }
    $end = reset($tarif);
    $last = $first = end($tarif);
    #cuj "https://ehpad.home/ajax/get-evolution-menusuelle-des-tarifs/33007" a '' 1;#red-bonobo
    $resultList = [];
    foreach ($chartData['xAxe'] as $xAxe) {
        if (isset($tarif[$xAxe])) {
            $resultList[$xAxe]['nombre_revision'] = 1;
            $resultList[$xAxe]['pr_prixmin'][] = $last = $tarif[$xAxe];
            $a = 1;
        } elseif ('étaler la confiture en absence de variations') {
            $resultList[$xAxe]['nombre_revision'] = 1;
            $resultList[$xAxe]['pr_prixmin'][] = $last;#dernier update
            $a = 1;
        } else {
            $resultList[$xAxe]['nombre_revision'] = 0;
            $resultList[$xAxe]['pr_prixmin'] = [];
        }
    }
    $a = 1;
    /*
    foreach( $chambres as $chambre ) {
    if( !in_array( $chambre->date_modification, $chartData['xAxe']) ) {
    $resultList[$chartData['xAxe'][0]]['nombre_revision'] += 1;
    $resultList[$chartData['xAxe'][0]]['tarif_chambre_simple'][] = $chambre->field_tarif_chambre_simple_value;
    } else {
    $resultList[$chambre->date_modification]['nombre_revision'] += 1;
    $resultList[$chambre->date_modification]['tarif_chambre_simple'][] = $chambre->field_tarif_chambre_simple_value;
    }
    }
    */
    $dernierRevisionTarif = null;
    foreach ($resultList as $key => $result) {
        if ($result['nombre_revision'] == 1) {
            $dernierRevisionTarif = $result['pr_prixmin'][0];
            $resultList[$key]['pr_prixmin'] = $resultList[$key]['pr_prixmin'][0];
        } else {
            if ($result['nombre_revision'] > 1) {
                $dernierRevisionTarif = $result['pr_prixmin'][count($result['pr_prixmin']) - 1];
                $resultList[$key]['pr_prixmin'] = array_sum($resultList[$key]['pr_prixmin']);
            }
        }

        if ($result['nombre_revision'] == 0 && $dernierRevisionTarif != null) {
            $resultList[$key]['nombre_revision'] = 1;
            $resultList[$key]['pr_prixmin'] = $dernierRevisionTarif;
        }
    }

    $dernierMoyenTarif = null;
    $first=reset($resultList);
    $last = $first['pr_prixmin']/$first['nombre_revision'];


    foreach ($chartData['xAxe'] as $mois) {
        #if(!in_array($mois,$moisHasPrices))$moisHasPrices[]=$mois;
        if (isset($resultList[$mois]['pr_prixmin'])) {
            $last = $resultList[$mois]['pr_prixmin']/$resultList[$mois]['nombre_revision'];
        } elseif (0 and $last) {
            $resultList[$mois]['pr_prixmin'] = $last;
            #report du précédent ..étalage de la confiture
        }else{#bah rien
            #$chartData['dataResidence'][] = $dernierMoyenTarif;
        }
        $chartData['dataResidence'][] = $last;
    }


    /*
            if ($data['nombre_revision'] != 0) {
                $dernierMoyenTarif = round($data['tarif_chambre_simple'] / $data['nombre_revision'], 2);
                $chartData['dataResidence'][] = $dernierMoyenTarif;
            } else {
                $chartData['dataResidence'][] = $dernierMoyenTarif;
            }
    */

    // DEPARTEMENTS
    $query = db_select('node', 'n');
    $query->condition('n.type', "residence", '=');
    $query->innerjoin('field_data_field_departement', 'd', 'd.entity_id = n.nid and d.field_departement_tid = :departmentId', array(':departmentId' => $residence[$residenceId]->field_departement['und'][0]['tid']));
    $query->leftjoin('field_data_field_residence_id', 'rc', 'rc.field_residence_id_value = n.nid', array());
    $query->leftjoin('node', 'c', 'rc.entity_id = c.nid', array());
    $query->fields('n', array('nid', 'title'));
    $query->fields('c', array('nid', 'title'));
    $residences = fetchAll($query);

    $chambresNids = [];
    foreach ($residences as $r) {
        $chambresNids[] = $r->c_nid;
    }
    $chambresNids=array_filter($chambresNids);
    /*
    $query = db_select('field_revision_field_date_de_modification', 'dm');
    $query->leftJoin('field_revision_field_tarif_chambre_simple', 'cs', 'cs.revision_id = dm.revision_id', array());
    $query->condition('dm.entity_id', $chambresNids, 'IN');
    $query->addExpression("DATE_FORMAT(dm.field_date_de_modification_value, '%Y-%m')", 'date_modification');
    */

    if (1 and 'new') {
        $first = $prixParMois = [];
        $depChambres = Alptech\Wip\fun::sql("select entity_id as k,field_pr_prixmin_value as v,nr.timestamp as t from field_revision_field_pr_prixmin cs inner join node_revision nr on cs.revision_id=nr.vid where cs.entity_id in (" . implode(',', $chambresNids) . ") and cs.field_pr_prixmin_value IS NOT NULL order by timestamp asc");
        $mois=[];
        foreach ($depChambres as $t) {
            $mois[]=$month = date('Y-m', $t['t']);
            if (!isset($first[$t['k']])) {
                $first[$t['k']] = $month;
            }
            $prixParMois[$month][$t['k']] = $t['v'];#toujours la dernière valeur ,
        }
        $mois=array_unique($mois);
        $moisHasPrices=[];
        foreach ($chambresNids as $cid) {
            $last = $started = 0;
            foreach ($chartData['xAxe'] as $mois) {
                if(!in_array($mois,$moisHasPrices))$moisHasPrices[]=$mois;
                if (isset($prixParMois[$mois][$cid])) {
                    $last = $prixParMois[$mois][$cid];
                } elseif ($last) {
                    $prixParMois[$mois][$cid] = $last;
                    #report du précédent ..étalage de la confiture
                }else{#bah rien

                }
            }
        }
        ksort($prixParMois);
#$diff=array_diff($chartData['xAxe'],$moisHasPrices);foreach($diff )
        $last = reset($prixParMois);
        $last = round(array_sum($last) / count($last), 2);
        foreach ($chartData['xAxe'] as $mois) {
            if (isset($prixParMois[$mois])) {
                $last = round(array_sum($prixParMois[$mois]) / count($prixParMois[$mois]), 2);
            }
            $chartData['dataDepartement'][] = $last;#prend la dernière valeur, de toutes façons
        }
        $a = 1;
    } else{
        $query = db_select('field_revision_field_pr_prixmin', 'cs');
        $query->condition('cs.entity_id', $chambresNids, 'IN');
        $query->isNotNull('cs.field_pr_prixmin_value');
        $query->join('node_revision', 'nr', 'cs.revision_id = nr.vid', []);#$query->addExpression("DATE_FORMAT(FROM_UNIXTIME(nr.timestamp), '%Y-%m')", 'date_modification');
        //$query->addExpression("COUNT(dm.entity_id)", 'nombre_revision');
        //$query->addExpression("SUM(CAST(cs.field_tarif_chambre_simple_value AS DECIMAL(8,2)))", 'tarif_chambre');
        $query->fields('cs', ['entity_id', 'field_pr_prixmin_value']);
        $query->addExpression("DATE_FORMAT(FROM_UNIXTIME(nr.timestamp), '%Y-%m')", 'date_modification');
        //$query->groupBy('date_modification');
        $query->orderBy('date_modification', 'asc');
        $departementChambres = fetchAll($query);
#cuj http://ehpad.home/ajax/get-evolution-menusuelle-des-tarifs/33007 a {} 1

        $a = 1;

        $x = [];
        foreach ($departementChambres as $t) {
            $x[$t->date_modification][$t->entity_id] = $t->field_pr_prixmin_value;
        }

        $first = reset($x);
        $last = round(array_sum($first) / count($first), 2);
        foreach ($chartData['xAxe'] as $key => $xAxe) {
            if (isset($x[$xAxe])) {
                $last = round(array_sum($x[$xAxe]) / count($x[$xAxe]), 2);
            }
            $chartData['dataDepartement'][] = $last;#prend la dernière valeur, de toutes façons
        }
    }

    if(0){
#md5(serialize($departementChambres))=="059fd9718b113fc2ac8096427532c5c6", 1562 entités
        $resultList = [];
        foreach ($chartData['xAxe'] as $key => $xAxe) {
            $resultList[$xAxe]['nombre_revision'] = 0;
            $resultList[$xAxe]['pr_prixmin'] = 0;
            foreach ($departementChambres as $departementChambre) {
                if ($departementChambre->date_modification <= $xAxe) {
                    $resultList[$xAxe]['nombre_revision'] += 1;
                    $resultList[$xAxe]['pr_prixmin'] += $departementChambre->field_pr_prixmin_value;
                }
            }
        }

        foreach ($resultList as $data) {
            if ($data['nombre_revision'] != 0) {
                $chartData['dataDepartement'][] = round($data['pr_prixmin'] / $data['nombre_revision'], 2);
            }
        }
    }

// RESIDENCE CONCURRENTS DIRECT :: nb :: ne sont pas forcément les mêmes que les résultats provenant de geoloc
    $query = db_select('node', 'n');
#tri déjà effectué :)
#$query->join('field_data_field_statut', 's', 's.entity_id = n.nid and s.field_statut_value = :statut', array(':statut' => $residence[$residenceId]->field_statut['und'][0]['value']));
    $query->join('field_data_field_location', 'l', 'l.entity_id = n.nid', array());
    $query->join('field_data_field_residence_id', 'rc', 'rc.field_residence_id_value = n.nid', array());
    $query->join('field_data_field_pr_prixmin', 'cs', 'cs.entity_id = rc.entity_id and cs.field_pr_prixmin_value IS NOT NULL', array());
    $query->join('field_data_field_latitude', 'lat', 'lat.entity_id = n.nid', array());
    $query->join('field_data_field_longitude', 'lng', 'lng.entity_id = n.nid', array());
    $query->join('field_data_field_type', 't', 't.entity_id = n.nid', array());
    $query->condition('field_type_value','notEhpad','=');
    $query->fields('n', array('nid'));$query->fields('rc', array('entity_id'));
    $query->condition('n.type', "residence", '=');
    $query->condition('n.nid', $residenceId, '<>');#not the same
    $query->condition('n.nid', $_closestResidences, 'in');#in closest found residences
    $query->orderBy('FIELD(nid,'.implode(',',$_closestResidences).')');#$ko,
    /*
    $query->addExpression('(6371 * acos(cos(radians(lat.field_latitude_value)) * cos(radians(:latitude) ) * cos(radians(:longitude) -radians(lng.field_longitude_value)) + sin(radians(lat.field_latitude_value)) * sin(radians(:latitude))))', 'distance', array(':latitude' => $residence[$residenceId]->field_latitude['und'][0]['value'], ':longitude' => $residence[$residenceId]->field_longitude['und'][0]['value']));
    $query->orderBy('distance', 'ASC');
    */
    $query->range(0, $limit);
    $residencesConcurrentes = fetchAll($query);

    $resIds=$chambresRCNids = [];#!=$chambresNids( toutes chambres du département )
    foreach ($residencesConcurrentes as $r) {
        $resIds[] = intval($r->nid);
        $chambresRCNids[] = intval($r->entity_id);
        $ch2res[intval($r->entity_id)]=intval($r->nid);#
    }

    if($_tarifss_1){
        $k1=array_keys($_t3);$k2=$resIds;
        sort($k0);sort($k1);sort($k2);
        $kdelta01=array_diff($k0,$k1);
        $kdelta12=array_diff($k1,$k2);
        if ($kdelta12) {#
            $a='not the same closest résidences -- or else are the same keys';
        }
    }

#use :: node_revision nr timestamp instead

    $query=db_select('field_revision_field_pr_prixmin', 'cs');
    $query->condition('cs.entity_id', $chambresRCNids, 'IN');
    $query->join('node_revision', 'nr', 'cs.revision_id = nr.vid', []);#$query->addExpression("DATE_FORMAT(FROM_UNIXTIME(nr.timestamp), '%Y-%m')", 'date_modification');
    //$query->addExpression("COUNT(dm.entity_id)", 'nombre_revision');
    //$query->addExpression("SUM(CAST(cs.field_tarif_chambre_simple_value AS DECIMAL(8,2)))", 'tarif_chambre');
    $query->fields('cs', array('entity_id', 'field_pr_prixmin_value'));
    $query->fields('nr', ['timestamp']);
    $query->addExpression("DATE_FORMAT(FROM_UNIXTIME(nr.timestamp), '%Y-%m')", 'date_modification');
    //$query->groupBy('date_modification');
    $query->orderBy('nr.timestamp', 'assc');
    $residencesConcurrentesChambres = fetchAll($query);#221

    $chTarifsPerDate=[];
    foreach($residencesConcurrentesChambres as $t){
        $new=$t->field_pr_prixmin_value;
        if(isset($chTarifsPerDate[$t->date_modification][$ch2res[$t->entity_id]]) and $chTarifsPerDate[$t->date_modification][$ch2res[$t->entity_id]]!=$new){
            $previous=$chTarifsPerDate[$t->date_modification][$ch2res[$t->entity_id]];
            $a='overiden';
        }
        $chTarifsPerDate[$t->date_modification][$ch2res[$t->entity_id]]=$new;
    }

    if($_tarifss_1){
        $_tarifss_2=end($chTarifsPerDate);$as[2]=array_sum($_tarifss_2);#827.99
        $_nonPresentsDansFonction1=array_diff(array_keys($_tarifss_1),array_keys($_t3));
        $_nonPresentsDansFonction2=array_diff(array_keys($_tarifss_2),array_keys($_t3));
        ksort($_tarifss_1);ksort($_tarifss_2);
        $diff=$as[3]-$as[1];
        if($diff){
            $d32=array_diff($_t3,$_tarifss_2);;
            $d1=array_diff($_tarifss_1,$_tarifss_2);
            $d2=array_diff($_tarifss_2,$_tarifss_1);
            #$a=1;#33135,33151,33169,33171
            $a=1;
        }
    }

    $x=reset($chTarifsPerDate);
    $first=$last=round(array_sum($x)/count($x),2);
    $x=end($chTarifsPerDate);
    $final=round(array_sum($x)/count($x),2);

    $resultList = [];#
    foreach ($chartData['xAxe'] as $key => $xAxe) {
        if(isset($chTarifsPerDate[$xAxe])){
            $last=round(array_sum($x)/count($x),2);
            $chartData['dataResidencesConcurrents'][] =$last;
        }else{
            $chartData['dataResidencesConcurrents'][] =$last;
        }
        continue;

        $resultList[$xAxe]['nombre_revision'] = 0;
        $resultList[$xAxe]['pr_prixmin'] = 0;
        foreach ($residencesConcurrentesChambres as $residenceConcurrenteChambre) {
            if ($residenceConcurrenteChambre->date_modification <= $xAxe) {
                $resultList[$xAxe]['nombre_revision'] += 1;
                $resultList[$xAxe]['pr_prixmin'] += $residenceConcurrenteChambre->field_tarif_chambre_simple_value;
            }
        }
    }
    /*#red-bonobo end
    foreach ($resultList as $data) {
        if ($data['nombre_revision'] != 0) {
            $chartData['dataResidencesConcurrents'][] = round($data['tarif_chambre_simple'] / $data['nombre_revision'], 2);
        } else {
            $chartData['dataResidencesConcurrents'][] = $chartData['dataResidencesConcurrents'][count($chartData['dataResidencesConcurrents']) - 1];#repeat previous information
        }
    }
    */
// RESULT
    $endC=$chartData['dataResidence'];
    $endCi=$chartData['dataResidencesConcurrents'];
    $tmoyConcurrence=array_sum($_t3)/count($_t3);
    $lastAxis=end(array_keys($chartData['xAxe']));
    $chartData['dataResidencesConcurrents'][$lastAxis]=round($tmoyConcurrence,2);
    #=$_t3;
    echo json_encode($chartData);#dataResidence
    die;//#red-bonobo
}
#cuj http://ehpad.silverpricing.fr/ajax/get-evolution-menusuelle-des-tarifs/33007 a {} 1
