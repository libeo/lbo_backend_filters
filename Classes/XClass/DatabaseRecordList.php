<?php

namespace Libeo\LboBackendFilters\XClass;

use TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DatabaseRecordList extends \TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList
{
    /**
     * @inheritdoc
     */
    public function generateList() : string
    {
        $config = $this->getTSConfig();

        $content = parent::generateList();

        if (!$config['filters.']) {
            return $content;
        }

        if (GeneralUtility::_POST('clearFilter')) {
            $_POST = [];
        }

        $formsElements = [];
        foreach ($config['filters.'] as $tableName => $fields) {
            $tableName = rtrim($tableName, '.');
            $processedTca = $this->getProcessedTca($tableName);
            foreach ($fields as $filterName => $options) {
                $fieldName = $options['fieldname'];
                $fieldNameHtml = $tableName . '[' . $fieldName . ']';
                $label = $options['label'] ?: trim($filterName, '.');
                $newLine = (bool) $options['new-line'];

                $submittedValues = GeneralUtility::_POST($tableName);
                $fieldConfig = self::getTcaOfFieldConfig($tableName, $fieldName);
                if ($fieldConfig['type'] === 'input') {
                    $formField = '<input type="text" class="form-control" name="' . $fieldNameHtml . '" value="' . $submittedValues[$fieldName] . '">';
                } elseif ($fieldConfig['type'] === 'select') {
                    $formField = '<select class="form-control" name="' . $fieldNameHtml . '">';

                    $formField .= '<option value="-1"></option>';
                    foreach ($processedTca['columns'][$fieldName]['config']['items'] as $item) {
                        $selected = '';
                        if ($item[1] === '-1') {
                            continue;
                        }
                        if ($submittedValues[$fieldName] && intval($submittedValues[$fieldName]) === intval($item[1])) {
                            $selected = 'selected';
                        }
                        $formField .= '<option value="' . $item[1] . '" ' . $selected . '>' . $item[0] . '</option>';
                    }

                    $formField .= '</select>';
                } elseif ($fieldConfig['type'] === 'check') {
                    $checked = '';
                    if (intval($submittedValues[$fieldName]) === 1) {
                        $checked = 'checked';
                    }
                    $formField = '<input type="checkbox" class="checkbox-input" name="' . $fieldNameHtml . '" value="1" ' . $checked . '>';
                }

                $classSize = 'col-sm-2';
                if ($options['size'] == 'small') {
                    $classSize = 'col-sm-1';
                }
                if ($options['size'] == 'large') {
                    $classSize = 'col-sm-3';
                }

                if (!empty($newLine)) {
                    $formsElements[] = '<div class="w-100 mt-2"></div>';
                }
                $formsElements[] = '<div class="' . $classSize . ' col-xs-12"><label for="' . $fieldNameHtml . '">' . $label . '</label><div class="form-control-clearable">' . $formField . '</div></div>';
            }
        }
        $newForm[] = '<form action="' . htmlspecialchars($this->listURL()) . '" method="post" name="fieldSelectBox">';
        $newForm[] = '<div class="panel panel-default">';
        $newForm[] = '<div class="panel-body">';
        $newForm[] = '<div class="row">';
        $newForm[] = implode($formsElements);
        $newForm[] = '<div class="col-sm-2 col-xs-12 align-self-end">';
        $newForm[] = '<div class="form-control-wrap d-flex justify-content-between">';
        $newForm[] = '<input type="submit" name="customFilter" class="btn btn-default" value="Filtrer"> ';
        $newForm[] = '<input type="submit" name="clearFilter" class="btn btn-default" value="Réinitialiser">';
        $newForm[] = '</div>';
        $newForm[] = '</div>';
        $newForm[] = '</div>';
        $newForm[] = '</div>';
        $newForm[] = '</div>';
        $newForm[] = '</form>';

        return implode($newForm) . $content;
    }

    /**
     * @inheritdoc
     */
    public function getQueryBuilder(
        string $table,
        int $pageId,
        array $additionalConstraints,
        array $fields,
        bool $addSorting,
        int $firstResult,
        int $maxResult
    ): QueryBuilder {
        $additionalConstraints = $this->getAdditionnalConstraints($table, $additionalConstraints);

        return parent::getQueryBuilder($table, $pageId, $additionalConstraints, $fields, $addSorting, $firstResult, $maxResult);
    }

    /**
     * @return array
     */
    private function getTSConfig(): array
    {
        return BackendUtility::getPagesTSconfig($this->id)['lbo_backend_filters.'] ?? [];
    }

    /**
     * @param $tablename
     * @param $fieldname
     * @return array
     */
    private static function getTcaOfFieldConfig($tablename, $fieldname): array
    {
        return $GLOBALS['TCA'][$tablename]['columns'][$fieldname]['config'] ?? [];
    }

    /**
     * @param string $tableName
     * @return array
     */
    private function getProcessedTca(string $tableName): array
    {
        $tcaSelectItems = GeneralUtility::makeInstance(TcaSelectItems::class);
        $result = $tcaSelectItems->addData(['tableName' => $tableName, 'processedTca' => $GLOBALS['TCA'][$tableName], 'databaseRow' => [], 'rootline' => []]);
        return $result['processedTca'] ?? [];
    }

    /**
     * @param mixed $table
     * @param array $additionalConstraints
     * @return array
     */
    private function getAdditionnalConstraints(string $table, array $additionalConstraints): array
    {
        if (GeneralUtility::_POST('customFilter')) {
            $config = $this->getTSConfig();
            foreach ($config['filters.'] as $tableName => $fieldsFilter) {
                $tableName = rtrim($tableName, '.');
                if ($table === $tableName) {
                    $submittedValues = GeneralUtility::_POST($tableName);
                    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
                    foreach ($submittedValues as $fieldName => $value) {
                        $fieldConfig = self::getTcaOfFieldConfig($tableName, $fieldName);

                        if ($fieldConfig['type'] === 'input') {
                            if ($value == '') {
                                continue;
                            }
                            $additionalConstraints[] = $queryBuilder->expr()->like(
                                $fieldName,
                                "'%" . $queryBuilder->escapeLikeWildcards($value) . "%'"
                            );
                        } elseif ($fieldConfig['type'] === 'select') {
                            if ($value == '-1' || $value == '0' || $value == '') {
                                continue;
                            }
                            $additionalConstraints[] = $queryBuilder->expr()->eq(
                                $fieldName,
                                $value
                            );
                        } elseif ($fieldConfig['type'] === 'check') {
                            if ($value === '1') {
                                $additionalConstraints[] = $queryBuilder->expr()->eq(
                                    $fieldName,
                                    $value
                                );
                            }
                        }
                    }
                }
            }
        }
        return $additionalConstraints;
    }

}
