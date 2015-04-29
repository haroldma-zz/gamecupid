<?php

use Illuminate\Database\Seeder;

class FunctionsSeeder extends Seeder
{

    public function run()
    {
        DB::transaction(function () {
            // helper function for calculating hotness
            DB::statement("DROP FUNCTION IF EXISTS calculateHotness;");
            DB::statement("DELIMITER $$
        CREATE FUNCTION calculateHotness(ups INTEGER, downs INTEGER, created DATETIME)
        RETURNS FLOAT
        BEGIN
            DECLARE votes, hotness, sign INTEGER;
            DECLARE seconds FLOAT;

            SET votes = ups - downs;

            IF (ABS(votes) > 1) then
                SET hotness = LOG10(ABS(votes));
            ELSE
                SET hotness = 1;
            END IF;

            IF (votes > 0) THEN
                SET sign = 1;
            ELSEIF (votes < 0) THEN
                SET sign = -1;
            ELSE
                SET sign = 0;
            END IF;
            SET seconds = UNIX_TIMESTAMP(created) - 1430006400;
            return ROUND(sign * hotness + seconds / 45000, 7);
        END$$
        DELIMITER ;");

            // helper function for calculating controversy
            DB::statement("DROP FUNCTION IF EXISTS calculateControversy;");
            DB::statement("DELIMITER $$
        CREATE FUNCTION calculateControversy(ups INTEGER, downs INTEGER)
        RETURNS FLOAT
        BEGIN
            DECLARE magnitude, balance FLOAT;

            IF (downs <= 0 or ups <= 0) then
               return 0;
            END IF;

            SET magnitude = ups + downs;
            if (ups > downs) then
                SET balance = downs / ups;
             ELSE
                  SET balance = ups / downs;
             END IF;

            return magnitude * balance;
        END$$");

            // Finally a helper function for calculating best
            DB::statement("DROP FUNCTION IF EXISTS calculateBest;");
            DB::statement("DELIMITER $$
        CREATE FUNCTION calculateBest(ups INTEGER, downs INTEGER)
        RETURNS FLOAT
        BEGIN
            DECLARE n, z, p, leftSide, rightSide, under FLOAT;

            SET n = ups + downs;

            IF (n = 0) THEN
                return 0;
            END IF;

            SET z = 1.281551565545; # 80% confidence
            SET p = ups / n;

            SET leftSide = p + 1/(2*n)*z*z;
            SET rightSide = z*sqrt(p*(1-p)/n + z*z/(4*n*n));
            SET under = 1+1/n*z*z;

            return (leftSide - rightSide) / under;
        END$$");
        });
    }
}