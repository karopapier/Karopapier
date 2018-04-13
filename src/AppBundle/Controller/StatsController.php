<?php

namespace AppBundle\Controller;

use AppBundle\Services\KaroQuery;
use PDO;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StatsController extends Controller
{
    /**
     * @Route("/addicts", name="addicts")
     * @Template("stats/addicts.html.twig")
     */
    public function indexAction(Request $request, KaroQuery $kq)
    {
        $by = $request->get('by', 'automoves');
        $bys = [
            'automoves' => 'automoves',
            'seit' => 'seit',
            'perday' => 'perday',
            'wollust' => 'max_wollust',
            'km' => 'distance',
        ];
        if (!array_key_exists($by, $bys)) {
            throw new HttpException('403', 'Nee, so nich');
        }
        $by = $bys[$by];

        $query = "SELECT U_ID,Login,Vorname,Nachname,Email,Color,Active,Invited,currentvisit,to_days(now())-to_days(signupdate) AS seit,to_days(now())-to_days(currentvisit) AS Besuch,Warned,automoves,automoves/(to_days(now())-to_days(signupdate)) AS perday, max_wollust, distance AS km FROM karo_user ORDER BY ".$by." DESC LIMIT 0,35";
        //$res = $kq->doQuery($query, ['by' => 'automoves']);
        $res = $kq->doQuery($query);
        $addicts = $res->fetchAll();

        foreach ($addicts as $key => $addict) {
            $uid = $addict['U_ID'];
            $subquery = "select count(U_ID) as anz from karo_teilnehmer WHERE U_ID=$uid";
            $subres = $kq->doQuery($subquery);
            $row = $subres->fetch(PDO::FETCH_ASSOC);
            $anz = $row['anz'];
            $addicts[$key]['anz'] = $anz;
        }


        $query = "SELECT Login, count(M_ID) AS zuege 
FROM karo_user 
JOIN karo_moves ON karo_user.U_ID = karo_moves.U_id
WHERE date > DATE_SUB(now(), INTERVAL 1 WEEK)
AND karo_moves.crash = FALSE
GROUP BY karo_user.U_ID
ORDER BY count(m_id) DESC
LIMIT 0,35";
        //$res = $kq->doQuery($query, ['by' => 'automoves']);
        $res = $kq->doQuery($query);
        $wollusts = $res->fetchAll();

        // replace this example code with whatever you need
        return [
            'addicts' => $addicts,
            'wollusts' => $wollusts,
        ];
    }
}
