<?php
namespace Zend\Filter;
/**
 * This filter adapts MySQL behaviour.
 * From docs ( http://dev.mysql.com/doc/refman/5.7/en/floating-point-types.html ):
 * MySQL permits a nonstandard syntax: FLOAT(M,D) or REAL(M,D) or DOUBLE PRECISION(M,D).
 * Here, “(M,D)” means than values can be stored with up to M digits in total,
 * of which D digits may be after the decimal point
 *
 * Class ToFloat
 * @package Zend\Filter
 */
class ToFloat extends AbstractFilter
{
    protected $options = [
        'digitsInTotal' => null,
        'digitsAfterDecPoint' => null
    ];

    /**
     * When passing options as an array, it's important
     * to give "digitsInTotal" before "digitsAfterDecPoint"
     * because of validation check.
     *
     * ToFloat constructor.
     * @param null $options
     */
    public function __construct($options = null)
    {
        if (!is_null($options)) {
            $this->setOptions($options);
        }
    }

    public function filter($value)
    {
        $value = (float)$value;
        if (is_null($this->getDigitsInTotal()) || is_null($this->getDigitsAfterDecPoint())) {
            return $value;
        }

        $exploded = explode('.', $value);
        $nonFractionalPart = $exploded[0];
        $maxNonFractionalPartLength = $this->getDigitsInTotal() - $this->getDigitsAfterDecPoint();

        if (strlen($nonFractionalPart) > $maxNonFractionalPartLength) {
            $msg = 'Out of range. Possible max length of non fractional part is %d';
            throw new \Exception(sprintf($msg, $maxNonFractionalPartLength));
        }

        return round($value, $this->getDigitsAfterDecPoint());
    }

    public function setDigitsInTotal($val)
    {
        $this->options['digitsInTotal'] = (int)$val;
    }

    public function getDigitsInTotal()
    {
        return $this->options['digitsInTotal'];
    }

    public function setDigitsAfterDecPoint($val)
    {
        if ($val >= $this->getDigitsInTotal()) {
            $msg = 'Number of digits after decimal point cannot be greater or equal than number of digits in total.';
            throw new \Exception($msg);
        }
        $this->options['digitsAfterDecPoint'] = (int)$val;
    }

    public function getDigitsAfterDecPoint()
    {
        return $this->options['digitsAfterDecPoint'];
    }
}