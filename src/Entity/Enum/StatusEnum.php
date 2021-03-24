<?php
namespace MyProject\DBAL;

namespace App\Entity\Enum;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class StatusEnum extends Type
{
    public const ENUM_STATUS = 'StatusEnum';

    public const STATUS_DOWNLOAD_NONE = 0;
    public const STATUS_DOWNLOAD_START = 1;
    public const STATUS_DOWNLOAD_SUCCESS = 2;
    public const STATUS_DOWNLOAD_ERROR = 3;
    public const STATUS_FILE_NO_EXIST = 4;

    public const STATUS = [
        self::STATUS_DOWNLOAD_NONE => '',
        self::STATUS_DOWNLOAD_START => 'En cours',
        self::STATUS_DOWNLOAD_SUCCESS => 'Téléchargé',
        self::STATUS_DOWNLOAD_ERROR => 'En erreur',
        self::STATUS_FILE_NO_EXIST => 'Fichier inexistant',
    ];


    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "ENUM(".implode(",", array_keys(self::STATUS)).")";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, array_keys(self::STATUS))) {
            throw new \InvalidArgumentException("Invalid status");
        }
        return $value;
    }

    public function getName()
    {
        return self::ENUM_STATUS;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}