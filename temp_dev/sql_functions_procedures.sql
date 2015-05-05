DROP FUNCTION IF EXISTS calculateTimezoneOffset;

DELIMITER $$
CREATE FUNCTION calculateTimezoneOffset(timezone VARCHAR(65535))
RETURNS INTEGER
BEGIN
    DECLARE minutes INTEGER;
    SET minutes = TIMESTAMPDIFF(MINUTE, UTC_TIMESTAMP(), CONVERT_TZ(UTC_TIMESTAMP(), "Etc/UTC", timezone));
    return minutes;
END$$
DELIMITER ;

DROP FUNCTION IF EXISTS calculateHotness;

DELIMITER $$
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
DELIMITER ;

DROP FUNCTION IF EXISTS calculateBest;

DELIMITER $$
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
END$$

DROP FUNCTION IF EXISTS calculateControversy;

DELIMITER $$
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
END$$



DROP PROCEDURE if exists GetNewPosts;

DELIMITER //
  CREATE PROCEDURE GetNewPosts(afterId INTEGER, count INTEGER)
  BEGIN
	   SELECT * 
	   FROM posts as cm
	   WHERE id < IF(0 = 0, (SELECT COUNT(*) + 1 FROM posts), 0) 
	   ORDER BY id DESC LIMIT 10;   
	END //
DELIMITER ;

DROP PROCEDURE if exists GetNewPostsByTimezone;

DELIMITER //
  CREATE PROCEDURE GetNewPostsByTimezone(afterId INTEGER, count INTEGER, fromTimezone SMALLINT, toTimezone SMALLINT)
  BEGIN
	   SELECT * 
	   FROM posts as cm
	   INNER JOIN (SELECT id as u_id, timezone FROM users) AS v ON u_id=user_id
	   WHERE id < IF(0 = 0, (SELECT COUNT(*) + 1 FROM posts), 0) 
	   AND calculateTimezoneOffset(timezone) between fromTimezone and toTimezone
	   ORDER BY id DESC LIMIT 10;   
	END //
DELIMITER ;


DROP procedure if exists GetHotPosts;

DELIMITER //
  CREATE PROCEDURE GetHotPosts(afterId INTEGER, count INTEGER)
  BEGIN
    SELECT * FROM 
		(SELECT *, if (id=afterId, @pos:=iterator, 0) as pos 
			FROM (SELECT @i:=@i+1 AS iterator, t.*
	    		FROM (SELECT *, calculateHotness(ups, downs, created_at) as sort FROM (SELECT * FROM posts AS cm
	    		INNER JOIN (SELECT post_id, 
			      SUM(IF(state = 1, 1, 0)) as ups,
			      SUM(IF(state = 0, 1, 0)) as downs
			    	FROM post_votes GROUP BY post_id) 
			    	AS v ON v.post_id=cm.id) e ORDER by sort desc) AS t, (SELECT @i:=0, @pos:=0) i) x) l 
		WHERE iterator > if (@pos = null, 0, @pos) LIMIT count;
   END //
DELIMITER ;

DROP procedure if exists GetHotPostsByTimezone;

DELIMITER //
  CREATE PROCEDURE GetHotPostsByTimezone(afterId INTEGER, count INTEGER, fromTimezone SMALLINT, toTimezone SMALLINT)
  BEGIN
    SELECT * FROM 
		(SELECT *, if (id=afterId, @pos:=iterator, 0) as pos 
			FROM (SELECT @i:=@i+1 AS iterator, t.*
	    		FROM (SELECT *, calculateHotness(ups, downs, created_at) as sort FROM (SELECT * FROM posts AS cm
	    		INNER JOIN (SELECT id as u_id, timezone FROM users) AS vv ON u_id=user_id
	    		INNER JOIN (SELECT post_id, 
			      SUM(IF(state = 1, 1, 0)) as ups,
			      SUM(IF(state = 0, 1, 0)) as downs
			    	FROM post_votes GROUP BY post_id) 
			    	AS v ON v.post_id=cm.id) e ORDER by sort desc) AS t, (SELECT @i:=0, @pos:=0) i) x
			    	WHERE calculateTimezoneOffset(timezone) between fromTimezone and toTimezone) l 
		WHERE iterator > if (@pos = null, 0, @pos) LIMIT count;
   END //
DELIMITER ;



DROP procedure if exists GetControversialPosts;

DELIMITER //
  CREATE PROCEDURE GetControversialPosts(afterId INTEGER, count INTEGER, fromDate DATETIME, toDate DATETIME)
  BEGIN
    SELECT * FROM 
		(SELECT *, if (id=afterId, @pos:=iterator, 0) as pos 
			FROM (SELECT @i:=@i+1 AS iterator, t.*
	    		FROM (SELECT *, calculateControversy(ups, downs) as sort FROM (SELECT * FROM posts AS cm
	    		INNER JOIN (SELECT post_id, 
			      SUM(IF(state = 1, 1, 0)) as ups,
			      SUM(IF(state = 0, 1, 0)) as downs
			    	FROM post_votes
			    	GROUP BY post_id) AS v
			      	ON v.post_id=cm.id) e ORDER by sort desc) AS t, (SELECT @i:=0, @pos:=0) i) x 
			WHERE sort > 0 and created_at BETWEEN fromDate and toDate) l 
		WHERE iterator > if (@pos = null, 0, @pos) LIMIT count;
   END //
DELIMITER ;


DROP procedure if exists GetControversialPostsByTimezone;

DELIMITER //
  CREATE PROCEDURE GetControversialPostsByTimezone(afterId INTEGER, count INTEGER, fromDate DATETIME, toDate DATETIME, fromTimezone SMALLINT, toTimezone SMALLINT)
  BEGIN
    SELECT * FROM 
		(SELECT *, if (id=afterId, @pos:=iterator, 0) as pos 
			FROM (SELECT @i:=@i+1 AS iterator, t.*
	    		FROM (SELECT *, calculateControversy(ups, downs) as sort FROM (SELECT * FROM posts AS cm
	    		INNER JOIN (SELECT id as u_id, timezone FROM users) AS vv ON u_id=user_id
	    		INNER JOIN (SELECT post_id, 
			      SUM(IF(state = 1, 1, 0)) as ups,
			      SUM(IF(state = 0, 1, 0)) as downs
			    	FROM post_votes
			    	GROUP BY post_id) AS v
			      	ON v.post_id=cm.id) e ORDER by sort desc) AS t, (SELECT @i:=0, @pos:=0) i) x 
			WHERE sort > 0 and created_at BETWEEN fromDate and toDate and calculateTimezoneOffset(timezone) between fromTimezone and toTimezone) l 
		WHERE iterator > if (@pos = null, 0, @pos) LIMIT count;
   END //
DELIMITER ;


DROP procedure if exists GetTopPosts;

DELIMITER //
  CREATE PROCEDURE GetTopPosts(afterId INTEGER, count INTEGER, fromDate DATETIME, toDate DATETIME)
  BEGIN
    SELECT * FROM 
		(SELECT *, if (id=afterId, @pos:=iterator, 0) as pos 
			FROM (SELECT @i:=@i+1 AS iterator, t.*
	    		FROM (SELECT *, ups - downs as sort FROM (SELECT * FROM posts AS cm
	    		INNER JOIN (SELECT post_id, 
			      SUM(IF(state = 1, 1, 0)) as ups,
			      SUM(IF(state = 0, 1, 0)) as downs
			    	FROM post_votes
			    	GROUP BY post_id) AS v
			      	ON v.post_id=cm.id) e ORDER by sort desc) AS t, (SELECT @i:=0, @pos:=0) i) x 
			WHERE sort > 0 and created_at BETWEEN fromDate and toDate) l 
		WHERE iterator > if (@pos = null, 0, @pos) LIMIT count;
   END //
DELIMITER ;

DROP procedure if exists GetTopPostsByTimezone;

DELIMITER //
  CREATE PROCEDURE GetTopPostsByTimezone(afterId INTEGER, count INTEGER, fromDate DATETIME, toDate DATETIME, fromTimezone SMALLINT, toTimezone SMALLINT)
  BEGIN
    SELECT * FROM 
		(SELECT *, if (id=afterId, @pos:=iterator, 0) as pos 
			FROM (SELECT @i:=@i+1 AS iterator, t.*
	    		FROM (SELECT *, ups - downs as sort FROM (SELECT * FROM posts AS cm
	    		INNER JOIN (SELECT id as u_id, timezone FROM users) AS vv ON u_id=user_id
	    		INNER JOIN (SELECT post_id, 
			      SUM(IF(state = 1, 1, 0)) as ups,
			      SUM(IF(state = 0, 1, 0)) as downs
			    	FROM post_votes
			    	GROUP BY post_id) AS v
			      	ON v.post_id=cm.id) e ORDER by sort desc) AS t, (SELECT @i:=0, @pos:=0) i) x 
			WHERE sort > 0 and created_at BETWEEN fromDate and toDate and calculateTimezoneOffset(timezone) between fromTimezone and toTimezone) l 
		WHERE iterator > if (@pos = null, 0, @pos) LIMIT count;
   END //
DELIMITER ;


DROP procedure if exists GetNewComments;

DELIMITER //
    CREATE PROCEDURE GetNewComments(postId INTEGER, parentId INTEGER, afterId INTEGER, count INTEGER)
    BEGIN
       SELECT * FROM comments WHERE id < IF(afterId = 0, (SELECT COUNT(*) + 1 FROM comments), afterId) and IF(parent_id = 0, post_id = postId and parent_id = 0, parent_id = parentId) ORDER BY id DESC LIMIT count;
   END //
DELIMITER ;


DROP procedure if exists GetHotComments;

DELIMITER //
  CREATE PROCEDURE GetHotComments(postId INTEGER, parentId INTEGER, afterId INTEGER, count INTEGER)
  BEGIN
    SELECT * FROM 
		(SELECT *, if (id=afterId, @pos:=iterator, 0) as pos 
			FROM (SELECT @i:=@i+1 AS iterator, t.*
	    		FROM (SELECT *, calculateHotness(ups, downs, created_at) as sort FROM (SELECT * FROM comments AS cm
	    		INNER JOIN (SELECT comment_id, 
			      SUM(IF(state = 1, 1, 0)) as ups,
			      SUM(IF(state = 0, 1, 0)) as downs
			    	FROM comment_votes
			    	GROUP BY comment_id) AS v
			      	ON v.comment_id=cm.id) e ORDER by sort desc) AS t, (SELECT @i:=0, @pos:=0) i) x 
			WHERE IF(parent_id = 0, post_id = postId and parent_id = 0, parent_id = parentId)) l 
		WHERE iterator > if (@pos = null, 0, @pos) LIMIT count;
   END //
DELIMITER ;


DROP procedure if exists GetBestComments;

DELIMITER //
  CREATE PROCEDURE GetBestComments(postId INTEGER, parentId INTEGER, afterId INTEGER, count INTEGER)
  BEGIN
    SELECT * FROM 
		(SELECT *, if (id=afterId, @pos:=iterator, 0) as pos 
			FROM (SELECT @i:=@i+1 AS iterator, t.*
	    		FROM (SELECT *, calculateBest(ups, downs) as sort FROM (SELECT * FROM comments AS cm
	    		INNER JOIN (SELECT comment_id, 
			      SUM(IF(state = 1, 1, 0)) as ups,
			      SUM(IF(state = 0, 1, 0)) as downs
			    	FROM comment_votes
			    	GROUP BY comment_id) AS v
			      	ON v.comment_id=cm.id) e ORDER by sort desc) AS t, (SELECT @i:=0, @pos:=0) i) x 
			WHERE IF(parent_id = 0, post_id = postId and parent_id = 0, parent_id = parentId)) l 
		WHERE iterator > if (@pos = null, 0, @pos) LIMIT count;
   END //
DELIMITER ;


DROP procedure if exists GetControversialComments;

DELIMITER //
  CREATE PROCEDURE GetControversialComments(postId INTEGER, parentId INTEGER, afterId INTEGER, count INTEGER)
  BEGIN
    SELECT * FROM 
		(SELECT *, if (id=afterId, @pos:=iterator, 0) as pos 
			FROM (SELECT @i:=@i+1 AS iterator, t.*
	    		FROM (SELECT *, calculateControversy(ups, downs) as sort FROM (SELECT * FROM comments AS cm
	    		INNER JOIN (SELECT comment_id, 
			      SUM(IF(state = 1, 1, 0)) as ups,
			      SUM(IF(state = 0, 1, 0)) as downs
			    	FROM comment_votes
			    	GROUP BY comment_id) AS v
			      	ON v.comment_id=cm.id) e ORDER by sort desc) AS t, (SELECT @i:=0, @pos:=0) i) x 
			WHERE IF(parent_id = 0, post_id = postId and parent_id = 0, parent_id = parentId)) l 
		WHERE iterator > if (@pos = null, 0, @pos) LIMIT count;
   END //
DELIMITER ;



DROP procedure if exists GetTopComments;

DELIMITER //
  CREATE PROCEDURE GetTopComments(postId INTEGER, parentId INTEGER, afterId INTEGER, count INTEGER)
  BEGIN
    SELECT * FROM 
		(SELECT *, if (id=afterId, @pos:=iterator, 0) as pos 
			FROM (SELECT @i:=@i+1 AS iterator, t.*
	    		FROM (SELECT *, ups - downs as sort FROM (SELECT * FROM comments AS cm
	    		INNER JOIN (SELECT comment_id, 
			      SUM(IF(state = 1, 1, 0)) as ups,
			      SUM(IF(state = 0, 1, 0)) as downs
			    	FROM comment_votes
			    	GROUP BY comment_id) AS v
			      	ON v.comment_id=cm.id) e ORDER by sort desc) AS t, (SELECT @i:=0, @pos:=0) i) x 
			WHERE IF(parent_id = 0, post_id = postId and parent_id = 0, parent_id = parentId)) l 
		WHERE iterator > if (@pos = null, 0, @pos) LIMIT count;
   END //
DELIMITER ;