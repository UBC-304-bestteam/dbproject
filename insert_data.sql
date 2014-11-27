insert into item values (1234,'George Sings the Classics','CD','classical','Sony',2000,'5.99',100);
insert into item values (9876,'Bob Rocks','CD','rock','Sony',2010,'10.99',53);
insert into item values (8888,'Cheryls Greatest Hits','DVD','pop','BMG',2005,'20.99',25);
insert into item values (7777,'The Dance Album','DVD','pop','BMG',2009,'25.99',36);
insert into item values (6666,'Big Red Truck','CD','country','Country Records',1995,'8.99',200);
insert into item values(1001,'Greatest Hits','CD','Rock','Company 1',2000,9.99,10);
insert into item values(5478,'Emotions','DVD','Pop','Fun time Records',1998,11.99,4);
insert into item values(6790,'Country Times','CD','Country','Company 1',2010,14.99,50);

insert into leadsinger values (1234,'George Costanza');
insert into leadsinger values (9876,'Bob Geldof');
insert into leadsinger values (8888,'Cheryl Smith');
insert into leadsinger values (7777,'Justin Timberlake');
insert into leadsinger values (6666,'Wayne Ford');
insert into leadsinger values(6790, 'Rusty Butters');
insert into leadsinger values(6790, 'Harold Lance');
insert into leadsinger values(1001, 'Neil Anderson');
insert into leadsinger values(5478, 'Mary Carlton');

insert into hassong values (1234,'Yellow Submarine');
insert into hassong values (1234,'Brick House');
insert into hassong values (1234,'Staying Alive');
insert into hassong values (9876,'Stop the Music');
insert into hassong values (9876,'Boom');
insert into hassong values (9876,'Guitars are Awesome');
insert into hassong values (8888,'Keep Dancing');
insert into hassong values (8888,'Lights are Bright');
insert into hassong values (7777,'Groovy');
insert into hassong values (7777,'Beats make me Dance');
insert into hassong values (6666,'My Truck is Out of Gas');
insert into hassong values (6666,'Where is my Beer');
insert into hassong values(5478, 'Happiness');
insert into hassong values(5478, 'Sadness');
insert into hassong values(5478, 'Anger');
insert into hassong values(6790, 'Barn yard brawl');
insert into hassong values(6790, 'Yeehaw');
insert into hassong values(6790, 'Corn field');

insert into orders values (87853,2014-06-08,'karen',67897,2017-08-08,2014-06-20,2014-06-19);
insert into orders values (99999,2014-11-08,'rachel',11111,2016-10-15,2014-11-20,null);
insert into orders values (59595,2013-04-25,'bobrocks',55555,2015-02-02,2013-05-05,2013-05-06);
insert into orders values(12345,'2014-11-01','customer01',897281,'2016-09-01','2014-12-01',null);
insert into orders values(01010,'2014-11-02','customer02',723848,'2015-01-12','2014-11-10','2014-11-10');

insert into purchaseitem values (87853,1234,2);
insert into purchaseitem values (99999,9876,10);
insert into purchaseitem values (59595,9876,20);
insert into purchaseitem values(01010,6790,1);
insert into purchaseitem values(12345,5478,2);
insert into purchaseitem values(12345,6790,1);
insert into purchaseitem values(12345,1001,4);

insert into customer values ('karen','password','Karen Smith','200 Main St.','123-4567');
insert into customer values ('rachel','1111','Rachel Jones', null, null);
insert into customer values ('bobrocks','password','Bob Geldof','100 Granville St.','555-1234');
insert into customer values('customer01','password','Jim Allison','1234 Main st', '6049989078');
insert into customer values('customer02','password','Holly Allison','786 Balsam Ave', '6049879134');

insert into returntransaction values (5555,2014-08-10,87853);
insert into returntransaction values(6767,'2014-11-11',01010);

insert into returnitem values (5555,1234,1);
insert into returnitem	values(6767,6790,1);
