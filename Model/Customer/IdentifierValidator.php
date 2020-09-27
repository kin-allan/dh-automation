<?php

namespace DigitalHub\Automation\Model\Customer;

class IdentifierValidator {

    /**
     * Validate if is a valid cpf
     * @param  string $cpf
     * @return boolean
     */
    public function isCPF($cpf)
    {
        if ($cpf) {
            $cpf = trim(preg_replace('/[^0-9]/', '', $cpf));
            if (strlen($cpf) == 11) {
                $sumd1 = 0;
                $sumd2 = 0;
                for ($i = 0; $i < strlen($cpf) - 1; $i++) {
                    if ($i < 9) {
                        $sumd1 += ($i + 1) * $cpf[$i];
                    }
                    $sumd2 += $i * $cpf[$i];
                }
                $fd = $sumd1 % 11;
                $fd = $fd == 10 ? 0 : $fd;
                $sd = $sumd2 % 11;
                $sd = $sd == 10 ? 0 : $sd;
                $validator = ($fd * 10) + $sd;

                if ($validator == (int) substr($cpf, 9, 11)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Validate if CNPJ is valid
     * @param  string $cnpj
     * @return boolean
     */
    public function isCNPJ($cnpj)
    {
        if ($cnpj) {
            $cnpj = trim(preg_replace('/[^0-9]/', '', $cnpj));
            if (strlen($cnpj) == 14) {
                $sumd1 = 0;
                $sumd2 = 0;
                $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
                for ($i = 0; $i < count($weights); $i++) {
                    if ($i > 0) {
                        $sumd1 += $weights[$i] * $cnpj[$i - 1];
                    }
                    $sumd2 += $weights[$i] * $cnpj[$i];
                }
                $fd = $sumd1 % 11;
                $fd = $fd < 2 ? 0 : (11 - $fd);
                $sd = $sumd2 % 11;
                $sd = $sd < 2 ? 0 : (11 - $sd);
                $validator = ($fd * 10) + $sd;
                if ($validator == (int) substr($cnpj, 12, 14)) {
                    return true;
                }
            }
        }

        return false;
    }
}
