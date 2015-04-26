<?php

use Illuminate\Database\Seeder;

class FunctionsSeeder extends Seeder
{

    public function run()
    {
        DB::transaction(function () {
            // Let's create a helper function for getting total upvotes
            DB::statement("DROP FUNCTION IF EXISTS getInviteUpvotes;");
            DB::statement("DELIMITER $$
        CREATE FUNCTION getInviteUpvotes(id INTEGER) RETURNS INTEGER
        BEGIN
          DECLARE votes INTEGER;
          SELECT count(*) INTO votes FROM invite_votes WHERE state = 1 and invite_id = id;
          RETURN votes;
        END$$
        DELIMITER ;");

            // Now for getting downvotes
            DB::statement("DROP FUNCTION IF EXISTS getInviteDownvotes;");
            DB::statement("DELIMITER $$
        CREATE FUNCTION getInviteDownvotes(id INTEGER) RETURNS INTEGER
        BEGIN
          DECLARE votes INTEGER;
          SELECT count(*) INTO votes FROM invite_votes WHERE state = 0 and invite_id = id;
          RETURN votes;
        END$$
        DELIMITER ;");

            // helper function for calculating hotness
            DB::statement("DROP FUNCTION IF EXISTS calculateHotness;");
            DB::statement("DELIMITER $$
        CREATE FUNCTION calculateHotness(ups INTEGER, downs INTEGER, created DATETIME)
        RETURNS INTEGER
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