<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="npc")
 **/
class Npc
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
     * @var string
     * @Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     * @Column(name="dialog", type="text")
     */
    private $dialog;
	
	/**
	 * @OneToMany(targetEntity="NpcLocation", mappedBy="npc", cascade={"persist"})
	 */
	private $npcLocations;

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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDialog()
    {
        return $this->dialog;
    }

    /**
     * @param string $dialog
     */
    public function setDialog($dialog)
    {
        $this->dialog = $dialog;
    }
	
	/**
	 * @return mixed
	 */
	public function getNpcLocations()
	{
		return $this->npcLocations;
	}
	
	/**
	 * @param mixed $npcLocations
	 */
	public function setNpcLocations($npcLocations)
	{
		$this->npcLocations = $npcLocations;
	}

}
