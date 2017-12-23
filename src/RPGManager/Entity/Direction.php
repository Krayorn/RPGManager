<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="direction")
 **/
class Direction
{
    /**
     * @var int
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ManyToOne(targetEntity="Place", inversedBy="directions", cascade={"persist"})
     * @JoinColumn(name="place_start_id", referencedColumnName="id")
     */
    private $placeStart;

    /**
     * @ManyToOne(targetEntity="Place", inversedBy="directions", cascade={"persist"})
     * @JoinColumn(name="place_arrival_id", referencedColumnName="id")
     */
    private $placeArrival;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPlaceStart()
    {
        return $this->placeStart;
    }

    /**
     * @param mixed $placeStart
     */
    public function setPlaceStart($placeStart)
    {
        $this->placeStart = $placeStart;
    }

    /**
     * @return mixed
     */
    public function getPlaceArrival()
    {
        return $this->placeArrival;
    }

    /**
     * @param mixed $placeArrival
     */
    public function setPlaceArrival($placeArrival)
    {
        $this->placeArrival = $placeArrival;
    }

}
