# RUID (Relatively Unique Identity)

This ID is based on the current millisecond time and some randomness allowing ordering of records by time with collision resistance. *Only works on 64bit platforms (you are using 64 bit now right?)*

## Why not use Primary keys?

We all remember the days when we created a MySQL table with a primary key.

	CREATE TABLE `user` {
		`id` int(11) NOT NULL AUTO_INCREMENT,
		....
	}

Then web scale came... and fault tolerance and decoupling and using a single database table with a single incrementing column started to fail us.

## UUID 

When dealing with distributed systems [many people](http://www.codinghorror.com/blog/2007/03/primary-keys-ids-versus-guids.html) often resort to using [UUID's](https://en.wikipedia.org/wiki/Universally_unique_identifier) to create a unique identifier without needing to check with a central authority (like a RDBMS primary key). UUID's however require 128 bits of storage and have no sequential data at all about them - they are just random gibberish.

	15ED815C-921C-4011-8667-7158982951EA
	70E2E8DE-500E-4630-B3CB-166131D35C21
	166131DD-3007-2130-CJD2-Z370E2E85C21

So which record came first? Who knows!

## Snowflake

Next was [Snowflake](https://blog.twitter.com/2010/announcing-snowflake) which cut the size down to 64 bits - but required running dedicated hardware to assign numbers. 

## RUID

This project takes these ideas and crams them all into a single 64 bit integer by using the current timestamp plus 16 bits from `/dev/urandom` to allow hundreds of inserts per millisecond (per table) with a very small chance of collision.

In other words, you can keep using your `bitint` RDBMS columns and still have a distributed system with no special services running to keep track of things.

Plus, working with integers is **fast**. If you do a lot of comparisons with ID's or relation logging in tables (like mapping tags to posts) then this approach will be much cheaper and faster than using UUID's.

Included are examples of using Go and PHP to generate a `RUID`

[David Pennington](http://davidpennington.me)
MIT License