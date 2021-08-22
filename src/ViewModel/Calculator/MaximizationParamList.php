<?php

namespace App\ViewModel\Calculator;

use App\Entity\Calculator\UserCalculation;
use App\Entity\Catalog\ResearchPoint;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Formatter;
use Symfony\Component\HttpFoundation\Request;

class MaximizationParamList extends AbstractViewModel
{
    /**
     * @var ResearchPoint[]
     */
    private $indexedResearchPoints;

    /**
     * @var string[]
     */
    private $options = [];

    /**
     * @var string|null
     */
    private $selected;

    /**
     * @param ResearchPoint[] $researchPoints
     */
    public function __construct(array $researchPoints)
    {
        foreach ($researchPoints as $researchPoint) {
            $this->indexedResearchPoints[$researchPoint->getCode()] = $researchPoint;
        }

        $creditIcon = Formatter::getIcon('/credit.svg');
        $this->options[UserCalculation::CREDIT_CODE] = "Credit ($creditIcon)";

        foreach ($this->indexedResearchPoints as $code => $researchPoint) {
            $icon = Formatter::getIcon($researchPoint->getIconUrl());
            $name = $researchPoint->getName();

            $this->options[$code] = "$name ($icon)";
        }

        $this->options[UserCalculation::TOTAL_EXPERIENCE_CODE] = "Total experience";
    }

    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void
    {
        $maximizationParam = $request->request->get('maximization-param');

        if (array_key_exists($maximizationParam, $this->indexedResearchPoints)
            || $maximizationParam === UserCalculation::CREDIT_CODE
            || $maximizationParam === UserCalculation::TOTAL_EXPERIENCE_CODE) {
            $this->selected = $maximizationParam;
        } else {
            $this->errors[] = 'Invalid maximization param';
        }
    }

    /**
     * @param UserCalculation $userCalculation
     */
    public function fillFromUserCalculation(UserCalculation $userCalculation): void
    {
        $this->selected = $userCalculation->getMaximizationParamCode();
    }

    /**
     * @param UserCalculation $userCalculation
     */
    public function fillUserCalculation(UserCalculation $userCalculation): void
    {
        $userCalculation->setMaximizationParamCode($this->selected);
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string[] $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return string|null
     */
    public function getSelected(): ?string
    {
        return $this->selected;
    }

    /**
     * @param string|null $selected
     */
    public function setSelected(?string $selected): void
    {
        $this->selected = $selected;
    }
}
