INSERT INTO temp_badhandovercell (id,city,subNetwork,cell,hour_id,前天小时数,昨天小时数,今天小时数,`准备切换失败数(最新)`,`执行切换失败数(最新)`,`准备切换失败数(总)`,`执行切换失败数(总)`,`准备切换失败数(今日)`,`执行切换失败数(今日)`,`严重程度`) SELECT
                a.id,
                a.city,
                a.subNetwork,
                a.cell,
                e.hour_id,
                b.`前天小时数`,
                c.`昨天小时数`,
                d.`今天小时数`,
                e.`准备切换失败数(最新)`,
                e.`执行切换失败数(最新)`,
                a.`准备切换失败数(总)`,
                a.`执行切换失败数(总)`,
                d.`准备切换失败数(今日)`,
                d.`执行切换失败数(今日)`,
                CASE WHEN c.`昨天小时数` IS NULL THEN 0 ELSE c.`昨天小时数` END + 
                CASE WHEN d.`今天小时数` IS NULL THEN 0 ELSE d.`今天小时数` END - 
                CASE WHEN b.`前天小时数` IS NULL THEN 0 ELSE b.`前天小时数` END + (a.`准备切换失败数(总)`+a.`执行切换失败数(总)`)/1000 AS 严重程度
            FROM
                (
                    SELECT
                        id,
                        city,
                        subNetwork,
                        cell,
                        sum(准备切换失败数) AS `准备切换失败数(总)`,
                        sum(执行切换失败数) AS `执行切换失败数(总)`
                    FROM
                        badHandoverCell_ex
                    WHERE
                        city = '$city'
                    AND cell IN (SELECT DISTINCT cell FROM badHandoverCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM badHandoverCell_ex ORDER BY id DESC LIMIT 1))
                    AND day_id >= DATE_ADD(
                        DATE_FORMAT(NOW(), '%Y-%m-%d'),
                        INTERVAL - 2 DAY
                    )
                    AND day_id <= DATE_FORMAT(NOW(), '%Y-%m-%d')
                    GROUP BY
                        subNetwork,
                        cell
                ) a
            LEFT JOIN (
                SELECT
                    cell,
                    COUNT(*) AS 前天小时数
                FROM
                    badHandoverCell_ex
                WHERE
                    city = '$city'
                AND cell IN (SELECT DISTINCT cell FROM badHandoverCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM badHandoverCell_ex ORDER BY id DESC LIMIT 1))
                AND day_id = DATE_ADD(
                    DATE_FORMAT(NOW(), '%Y-%m-%d'),
                    INTERVAL - 2 DAY
                )
                GROUP BY
                    subNetwork,
                    cell
            ) b ON a.cell = b.cell
            LEFT JOIN (
                SELECT
                    cell,
                    COUNT(*) AS 昨天小时数
                FROM
                    badHandoverCell_ex
                WHERE
                    city = '$city'
                AND cell IN (SELECT DISTINCT cell FROM badHandoverCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM badHandoverCell_ex ORDER BY id DESC LIMIT 1))
                AND day_id = DATE_ADD(
                    DATE_FORMAT(NOW(), '%Y-%m-%d'),
                    INTERVAL - 1 DAY
                )
                GROUP BY
                    subNetwork,
                    cell
            ) c ON a.cell = c.cell
            LEFT JOIN (
                SELECT
                    cell,
                    sum(准备切换失败数) AS `准备切换失败数(今日)`,
                    sum(执行切换失败数) AS `执行切换失败数(今日)`,
                    COUNT(*) AS 今天小时数
                FROM
                    badHandoverCell_ex
                WHERE
                    city = '$city'
                AND cell IN (SELECT DISTINCT cell FROM badHandoverCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM badHandoverCell_ex ORDER BY id DESC LIMIT 1))
                AND day_id = DATE_FORMAT(NOW(), '%Y-%m-%d')
                GROUP BY
                    subNetwork,
                    cell
            ) d ON a.cell = d.cell
            LEFT JOIN (
                SELECT
                    cell,
                    hour_id,
                    sum(准备切换失败数) AS `准备切换失败数(最新)`,
                    sum(执行切换失败数) AS `执行切换失败数(最新)`
                FROM
                    badHandoverCell_ex
                WHERE
                    city = '$city'
                AND cell IN (SELECT DISTINCT cell FROM badHandoverCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM badHandoverCell_ex ORDER BY id DESC LIMIT 1))
                AND day_id = DATE_FORMAT(NOW(), '%Y-%m-%d')
                AND hour_id = (SELECT hour_id FROM badHandoverCell_ex ORDER BY id DESC LIMIT 1)
                GROUP BY
                    subNetwork,
                    cell
            )e ON a.cell = e.cell
            ORDER BY
                严重程度 DESC;
            #LIMIT 30;