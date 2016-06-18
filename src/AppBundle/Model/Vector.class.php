<?php

namespace AppBundle\Model;

class Vector
{
    /** @var $x integer */
    private $x;
    /** @var $y integer */
    private $y;

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function setX($newx)
    {
        $this->x = $newx;
    }

    public function setY($newy)
    {
        $this->y = $newy;
    }

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function __toString()
    {
        return '(' . $this->x . '|' . $this->y . ')';
    }

    public function getXDirection()
    {
        if ($this->getX() != 0)
            return $this->getX() / abs($this->getX());
        else
            return 0;
    }

    public function getYDirection()
    {
        if ($this->getY() != 0)
            return $this->getY() / abs($this->getY());
        else
            return 0;
    }

    public function getLength()
    {
        return sqrt(pow($this->getX(), 2) + pow($this->getY(), 2));
    }

    public function decelerateX()
    {
        if (abs($this->getX()) != 0) {
            $this->setX((abs($this->getX()) - 1) * $this->getXDirection());
        }
    }

    public function decelerateY()
    {
        if (abs($this->getY()) != 0) {
            $this->setY((abs($this->getY()) - 1) * $this->getYDirection());
        }
    }

    public function decelerate()
    {
        $this->decelerateX();
        $this->decelerateY();
    }

    /**
     * returns all Vectors that are passed
     *
     */
    public function getPassedVectors()
    {
        return $this->getPassedVectorsGbham();
    }

    private function getPassedVectorsGbham()
        /*--------------------------------------------------------------
         * von quabla modifizierter Bresenham-Algorithmus
         * nach der auf wikipedia gefundenen C-Implementierung
         *---------------------------------------------------------------
         */
    {
        $dx = $this->getX();
        $dy = $this->getY();

        /* Vorzeichen des Inkrements bestimmen */
        $incx = ($dx > 0) ? 1 : (($dx < 0) ? -1 : 0);
        $incy = ($dy > 0) ? 1 : (($dy < 0) ? -1 : 0);

        // negative positiv machen
        if ($dx < 0) {
            $dx = -$dx;
        }
        if ($dy < 0) {
            $dy = -$dy;
        }

        /* feststellen, welche Entfernung grÃ¶ÃŸer ist */
        if ($dx > $dy) {
            /* x ist schnelle Richtung */
            $pdx = $incx;
            $pdy = 0;     /* pd. ist Parallelschritt */
            $qdx = 0;
            $qdy = $incy; /* qd. ist Querschritt     */
            $ddx = $incx;
            $ddy = $incy; /* dd. ist Diagonalschritt */
            $es = $dy;
            $el = $dx;   /* Fehlerschritte schnell, langsam */
        } else {
            /* y ist schnelle Richtung */
            $pdx = 0;
            $pdy = $incy; /* pd. ist Parallelschritt */
            $qdx = $incx;
            $qdy = 0;     /* qd. ist Querschritt     */
            $ddx = $incx;
            $ddy = $incy; /* dd. ist Diagonalschritt */
            $es = $dx;
            $el = $dy;   /* Fehlerschritte schnell, langsam */
        }

        /* Initialisierungen vor Schleifenbeginn */
        $x = 0;
        $y = 0;

        $v = new Vector($x, $y);
        $vecs[$v->__toString()] = $v;

        /* Pixel berechnen */
        $err = ($el - $es) / 2;
        /* das Vorzeichen von err gibt an, auf welcher Seite des Vektors sich
         * der Mittelpunkt des zuletzt betrachteten Kaestchens befindet. 
         * Bei Abweichung in "langsamer" Richtung ist err positiv,
         * und wir machen einen Schritt in "schneller" Richtung.
         * bei Abweichung in "schneller" Richtung ist err negativ,
         * und wir machen einen Schritt in "langsamer" Richtung.
         */

        //    echo "putting $x, $y at $err\n";
        do {
            if ($err < 0) {
                /* Fehlerterm wieder positiv (>=0) machen */
                /* Schritt in langsame Richtung, Querschritt */
                $err += $el;
                $x += $qdx;
                $y += $qdy;
            } elseif ($err > 0) {
                /* Schritt in schnelle Richtung, Parallelschritt */
                $err -= $es;
                $x += $pdx;
                $y += $pdy;
            } else {
                /* Schrit Diagonal */
                $err += $el;
                $err -= $es;
                $x += $ddx;
                $y += $ddy;
            }
            // echo "putting $x, $y at $err\n";
            $v = new Vector($x, $y);
            $vecs[$v->__toString()] = $v;
        } while ((abs($x) != $dx) || (abs($y) != $dy));
        return $vecs;
    }
}