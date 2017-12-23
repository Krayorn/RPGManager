<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="character_spell")
 **/
class CharacterSpell
{
    /**
     * @var int
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Character", inversedBy="characterSpells", cascade={"persist"})
     * @JoinColumn(name="character_id", referencedColumnName="id")
     */
    private $character;

    /**
     * @ManyToOne(targetEntity="Spell", inversedBy="characterSpells", cascade={"persist"})
     * @JoinColumn(name="spell_id", referencedColumnName="id")
     */
    private $spell;

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
    public function getCharacter()
    {
        return $this->character;
    }

    /**
     * @param mixed $character
     */
    public function setCharacter($character)
    {
        $this->character = $character;
    }

    /**
     * @return mixed
     */
    public function getSpell()
    {
        return $this->spell;
    }

    /**
     * @param mixed $spell
     */
    public function setSpell($spell)
    {
        $this->spell = $spell;
    }

}
