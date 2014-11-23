SET foreign_key_checks = 0;
DROP TABLE IF EXISTS returnitem, purchaseitem, leadsinger, returntransaction, hassong, item, customer, orders;
SET foreign_key_checks = 1;

create table item
	(upc int not null,
	title varchar(40) not null,
	item_type varchar(20) not null,
	category varchar(40) not null,
	company varchar(40),
	release_year int,
	price decimal(8,2) not null,
	stock int not null,
	PRIMARY KEY (upc));

create table leadsinger
	(upc int not null,
	singer_name varchar(40) not null,
	PRIMARY KEY (upc, singer_name),
	FOREIGN KEY (upc) REFERENCES item(upc));
 
create table hassong
	(upc int not null,
	title varchar(40) not null,
	PRIMARY KEY (upc,title),
	FOREIGN KEY (upc) REFERENCES item(upc));
 
create table customer
	(cid char(10) not null,
	pword varchar(20) not null,
	customer_name varchar(20) not null,
	address varchar(40),
	phone char(12),
	PRIMARY KEY (cid));

create table orders
	(receiptId int not null,
	odate date not null,
	cid char(10) not null,
	card int,
	expiryDate date,
	expectedDate date,
	deliveredDate date,
	PRIMARY KEY (receiptId),
	FOREIGN KEY (cid) REFERENCES customer(cid));
 
create table purchaseitem
	(receiptId int not null,
	upc int not null,
	quantity int not null,
	PRIMARY KEY (receiptId, upc),
	FOREIGN KEY (receiptId) REFERENCES orders(receiptId),
	FOREIGN KEY (upc) REFERENCES item(upc));


create table returntransaction
	(retid int not null,
	rdate date not null,
	receiptId int not null,
	PRIMARY KEY (retid),
	FOREIGN KEY (receiptId) REFERENCES orders(receiptId));

create table returnitem
	(retid int not null,
	upc int not null,
	quantity int not null,
	PRIMARY KEY (retid, upc),
	FOREIGN KEY (retid) REFERENCES returnTransaction(retid),
	FOREIGN KEY (upc) REFERENCES item(upc));

insert into item 
	values(1001,'Greatest Hits','CD','Rock','Company 1',2000,9.99,10);

insert into item 
	values(5478,'Emotions','DVD','Pop','Fun time Records',1998,11.99,4);

insert into item 
	values(6790,'Country Times','CD','Country','Company 1',2010,14.99,50);

insert into item 
	values(0001,'Who\'s Next','CD','Rock','Polydor',1971,100.00,1000);
    
insert into item 
	values(0002,'Quadrophenia','CD','Rock','Polydor',1973,50.99,1000);
    
insert into item 
	values(0003,'My Generation','CD','Rock','Polydor',1965,25.00,1000);
    
insert into leadsinger
	values(0001, "Roger Daltry");

insert into leadsinger
	values(0001, "Pete Townshend");
    
insert into leadsinger
	values(0002, "Roger Daltry");

insert into leadsinger
	values(0002, "Pete Townshend");
    
insert into leadsinger
	values(0003, "Roger Daltry");

insert into leadsinger
	values(0003, "Pete Townshend");

insert into leadsinger
	values(6790, "Rusty Butters");

insert into leadsinger
	values(6790, "Harold Lance");

insert into leadsinger
	values(1001, "Neil Anderson");

insert into leadsinger
	values(5478, "Mary Carlton");

insert into hassong
	values(5478, "Happiness");

insert into hassong
	values(5478, "Sadness");

insert into hassong
	values(5478, "Anger");

insert into hassong
	values(6790, "Barn yard brawl");

insert into hassong
	values(6790, "Yeehaw");

insert into hassong
	values(6790, "Corn field");

insert into customer 
	values('customer01','password','Jim Allison','1234 Main st', '6049989078');

insert into customer 
	values('customer02','password','Holly Allison','786 Balsam Ave', '6049879134');

insert into orders
	values(12345,'2014-11-01','customer01',897281,'2016-09-01','2014-12-01',null);
    
insert into orders
	values(12312,'2014-11-02','customer01',897281,'2016-09-01','2014-12-01',null);

insert into orders
	values(01010,'2014-11-02','customer02',723848,'2015-01-12','2014-11-10','2014-11-10');
    
insert into orders
	values(01011,'2014-11-02','customer02',723848,'2015-01-12',null,null);

insert into orders
	values(01012,'2014-11-02','customer02',723848,'2015-01-12',null,null);
    
insert into orders
	values(01013,'2014-11-02','customer02',723848,'2015-01-12','2014-11-10',null);

insert into purchaseitem
	values(01010,6790,1);

insert into purchaseitem
	values(12345,5478,2);
	
insert into purchaseitem
	values(12345,6790,1);

insert into purchaseitem
	values(12345,1001,4);
    
insert into purchaseitem
	values(01011,6790,4);
    
insert into purchaseitem
	values(12312,0001,5);
    
insert into purchaseitem
	values(01012,0001,5);

insert into purchaseitem
	values(01013,0002,2);
    
insert into purchaseitem
	values(01013,0003,4);

insert into returntransaction
	values(6767,'2014-11-11',01010);

insert into returnitem
	values(6767,6790,1);

commit;