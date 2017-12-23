<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="npc_location")
 **/
class NpcLocation
{
	/**
	 * @var int
	 * @Column(name="id", type="integer")
	 * @Id
	 * @GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @ManyToOne(targetEntity="Npc", inversedBy="npcLocations", cascade={"persist"})
	 * @JoinColumn(name="npc_id", referencedColumnName="id")
	 */
	private $npc;
	
	/**
	 * @var int
	 * @Column(name="number", type="integer")
	 */
	private $number;
	
	/**
	 * @ManyToOne(targetEntity="Place", inversedBy="npcLocations", cascade={"persist"})
	 * @JoinColumn(name="place_id", referencedColumnName="id")
	 */
	private $place;
	
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
	 * @return mixed
	 */
	public function getNpc()
	{
		return $this->npc;
	}
	
	/**
	 * @param mixed $npc
	 */
	public function setNpc($npc)
	{
		$this->npc = $npc;
	}
	
	/**
	 * @return int
	 */
	public function getNumber()
	{
		return $this->number;
	}
	
	/**
	 * @param int $number
	 */
	public function setNumber($number)
	{
		$this->number = $number;
	}
	
	/**
	 * @return mixed
	 */
	public function getPlace()
	{
		return $this->place;
	}
	
	/**
	 * @param mixed $place
	 */
	public function setPlace($place)
	{
		$this->place = $place;
	}
	
}