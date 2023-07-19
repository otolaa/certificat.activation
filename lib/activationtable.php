<?php
namespace Local\Certificat;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\BooleanField,
    Bitrix\Main\ORM\Fields\DatetimeField,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\StringField,
    Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class ActivationTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> ACTIVE_TO datetime mandatory
 * <li> CERTIFICAT_NUM int mandatory
 * <li> SECURITY_CODE int optional
 * <li> USER_EMAIL string(255) optional
 * <li> ACTIVE bool ('N', 'Y') optional default 'Y'
 * </ul>
 *
 * @package Local\Certificat
 **/

class ActivationTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_certificat_activation';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => Loc::getMessage('ACTIVATION_ENTITY_ID_FIELD')
                ]
            ),
            new DatetimeField(
                'ACTIVE_TO',
                [
                    'required' => true,
                    'title' => Loc::getMessage('ACTIVATION_ENTITY_ACTIVE_TO_FIELD')
                ]
            ),
            new IntegerField(
                'CERTIFICAT_NUM',
                [
                    'required' => true,
                    'title' => Loc::getMessage('ACTIVATION_ENTITY_CERTIFICAT_NUM_FIELD')
                ]
            ),
            new IntegerField(
                'SECURITY_CODE',
                [
                    'title' => Loc::getMessage('ACTIVATION_ENTITY_SECURITY_CODE_FIELD')
                ]
            ),
            new StringField(
                'USER_EMAIL',
                [
                    'validation' => [__CLASS__, 'validateUserEmail'],
                    'title' => Loc::getMessage('ACTIVATION_ENTITY_USER_EMAIL_FIELD')
                ]
            ),
            new BooleanField(
                'ACTIVE',
                [
                    'values' => array('N', 'Y'),
                    'default' => 'Y',
                    'title' => Loc::getMessage('ACTIVATION_ENTITY_ACTIVE_FIELD')
                ]
            ),
        ];
    }

    /**
     * Returns validators for USER_EMAIL field.
     *
     * @return array
     */
    public static function validateUserEmail()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }
}