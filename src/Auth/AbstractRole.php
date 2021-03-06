<?php

namespace Hail\Auth;


abstract class AbstractRole implements RoleInterface
{
    use AuthTrait;

    /**
     * @var static[][]
     */
    protected $belongTo;

    /**
     * @var SceneInterface
     */
    protected $scene;

    /**
     * @var \DateTime
     */
    protected $entryTime;

    /**
     * @param RoleInterface $role
     *
     * @return $this
     */
    public function addBelongTo(RoleInterface $role)
    {
        $name = $role->getType();
        $id = $role->getId();

        if (!isset($this->belongTo[$name][$id])) {
            if (!isset($this->belongTo[$name])) {
                $this->belongTo[$name] = [];
            }

            $this->belongTo[$name][$id] = $role;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $id
     *
     * @return RoleInterface|null
     */
    public function getBelongTo(string $name, string $id): ?RoleInterface
    {
        return $this->belongTo[$name][$id] ?? null;
    }

    /**
     * @param string $name
     *
     * @return RoleInterface[]
     */
    public function getBelongToByType(string $name): array
    {
        return $this->belongTo[$name] ?? [];
    }

    public function delBelongTo(string $name, string $id)
    {
        unset($this->belongTo[$name][$id]);
    }

    public function delBelongToByType(string $name)
    {
        $this->belongTo[$name] = [];
    }

    public function belongTo($name)
    {
        throw new \LogicException($this->getType() . ' not belong to any others');
    }

    public function entry(SceneInterface $to): RoleInterface
    {
        $from = $this->getScene();
        if ($from !== null) {
            $from->out($this);
        }

        $to->in($this);

        $this->entryTime = new \DateTime();

        return $this;
    }

    public function getScene(): ?SceneInterface
    {
        return $this->scene;
    }

    public function setScene(SceneInterface $scene = null)
    {
        $this->scene = $scene;

        return $this;
    }

    public function getEntryTime(): \DateTime
    {
        return $this->entryTime;
    }

    public function isExpire(): bool
    {
        if (($scene = $this->getScene()) === null) {
            return false;
        }

        $timeout = $scene->getTimeout();
        if ($timeout === 0) {
            return false;
        }

        $entry = (int) $this->entryTime->format('U');

        return time() < $entry + $timeout;
    }

}