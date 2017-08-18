#include<stdio.h>
#include<stdlib.h>

#include<sys/socket.h>
#include<sys/types.h>
#include<netinet/in.h>
#include<arpa/inet.h>
#include<string.h>

#include <mysql/mysql.h>
#include <my_global.h>

//initializing the mysql database connections
static char*host = "localhost";
static char*user = "root";
static char*password = "nicho123";
static char*dbname = "family_saaco";
unsigned int port = 3306;
char mysqlapi(char a);

int main(){
	//creating a socket
	int serv_socket = socket(AF_INET, SOCK_STREAM, 0);
	char sub[100]="Command sent successfully\n";
	
	// adress of the socket
	struct sockaddr_in server_address;
	server_address.sin_family = AF_INET;
	server_address.sin_port = htons(9000);
	server_address.sin_addr.s_addr = INADDR_ANY;

	//binding socket to the address and port number
	bind(serv_socket, (struct sockaddr *) &server_address, sizeof(server_address));

	//listening to the clients
	int listn;
	
	listn = listen(serv_socket, 10);

	if(listn==-1){
		printf("\nError while listening to clients.......\n");
	}else{
		printf("listening to clients.......\n");
	}
	
	int client_socket;
	char sent[300];
	FILE *records;
	while(1){
	
	//accepting client sockets
	client_socket = accept(serv_socket,NULL, NULL);
	
	if(client_socket==-1){
		printf("Error while accepting client requests......\n");
			}
	else{
		printf("Accepting client requests.......\n");
	}
	if(recv(client_socket, sent, sizeof(sent), 0)==-1){
		printf("Error while recieving .......");
	}else{

	//fetching the sum of all contributions af a particular member
		if(strncmp(sent,"contribution check",18)==0){
				
			MYSQL_RES *res;
			MYSQL_ROW row;
			MYSQL *conn;
		       conn= mysql_init(NULL);
	
	//connection to the mysql database
			if(!(mysql_real_connect(conn,host,user,password,dbname,port,NULL,0))){
					fprintf(stderr,"\n %s [%d]\n",mysql_error(conn),mysql_errno(conn));
					exit(1);
				}else{

	//splitting the string sent from the client
			char * pch;
			char *user;
 			pch = strtok (sent," ");
			int count=0;
			while (pch != NULL)
  			{
			if(count==2){
			printf ("%s\n",pch);
			user=pch;// assigning the variable user to the client user
			} 
    
			pch = strtok (NULL, " ");
			count++;
			}
			printf("connection to the database successful\n");
	
	//initializing the select query 		
				char query[]="SELECT sum(Contribution.contributionAmount) FROM Contribution, Member where Contribution.memberId=Member.memberId AND Member.username LIKE '%%%s%%'";

				char statement[1024];
				printf("%s connected....\n",user);
				sprintf(statement,query,user);

	//querying the select statement
				mysql_query(conn,statement);
					
				
					res = mysql_store_result(conn);
					int counter=0;

		//fetching from the database			
					while((row = mysql_fetch_row(res))!=NULL)
					{
					if(counter>1) continue;
					printf("%s\n",row[0]);
					char *hold;
					hold=row[0];// initializing the result from the database to the variable hold
					if(hold==NULL){
						char result[100]="Result not found\n";
						send(client_socket, result, sizeof(result), 0);
						printf("Client reply sent successfully\n");
					}else{
						strcat(hold,":/-\n");

		//sending the result back to the client
				 		send(client_socket, hold, sizeof(hold), 0);
						printf("Client reply sent successfully\n");
						counter++;
					}
					}
				
					mysql_free_result(res);
					mysql_close(conn);
				
				}
	//fetching the sum of all benefits of a particular user
		}else if(strncmp(sent,"benefit check",13)==0){
			MYSQL_RES *res2;
			MYSQL_ROW row2;
			MYSQL *conn;
		       conn= mysql_init(NULL);

	//connection to the mysql database
			if(!(mysql_real_connect(conn,host,user,password,dbname,port,NULL,0))){
					fprintf(stderr,"\n %s [%d]\n",mysql_error(conn),mysql_errno(conn));
					exit(1);
				}else{

	//splitting the string sent from the client
			char * pch2;
			char *user2;
 			pch2 = strtok (sent," ");
			int count2=0;
			while (pch2 != NULL)
  			{
			if(count2==2){
			printf ("%s\n",pch2);
			user2=pch2;// assigning the variable user to the client user2
			} 
    
			pch2 = strtok (NULL, " ");
			count2++;
			}
			printf("connection to the database successful\n");
			
	//initializing the select query
				char query2[]="SELECT sum(Benefit.benefitAmount) FROM Benefit, Member where Benefit.memberId=Member.memberId AND Member.username LIKE '%%%s%%'";

				char statement2[1024];
				printf("%s connected....\n",user2);
				sprintf(statement2,query2,user2);
	
	//querying the select statement	
				mysql_query(conn,statement2);
					res2 = mysql_store_result(conn);
					int counter2=0;
				
		//fetching from the database
					while((row2 = mysql_fetch_row(res2))!=NULL)
					{
					if(counter2>1) continue;
					printf("%s\n",row2[0]);
					char *hold2;
					hold2=row2[0];// initializing the result from the database to the variable hold2
					if(hold2==NULL){
							char result2[100]="Result not found\n";
							send(client_socket, result2, sizeof(result2), 0);
							printf("Client reply sent successfully\n");
					}else{
						strcat(hold2,":/-\n");

		//sending the result back to the client
				 	send(client_socket, hold2, sizeof(hold2), 0);
					printf("Client reply sent successfully\n");
					counter2++;
					}
					}
					mysql_free_result(res2);
					mysql_close(conn); //closing the mysql database connection
				
				}

	//fetching the loan status of a particular user
			}else if(strncmp(sent,"loan status",11)==0){
			MYSQL_RES *res3;
			MYSQL_ROW row3;
			MYSQL *conn;
		       conn= mysql_init(NULL);

	//connection to the mysql database
			if(!(mysql_real_connect(conn,host,user,password,dbname,port,NULL,0))){
					fprintf(stderr,"\n %s [%d]\n",mysql_error(conn),mysql_errno(conn));
					exit(1);
			}else{

	//splitting the string sent from the client
			char * pch3;
			char *user3;
 			pch3 = strtok (sent," ");
			int count3=0;
			while (pch3 != NULL)
  			{
			if(count3==2){
			printf ("%s\n",pch3);
			user3=pch3;
			} 
    
			pch3 = strtok (NULL, " ");
			count3++;
			}
			printf("connection to the database successful\n");
			
	//initializing the select query
				char query3[]="SELECT loanStatus FROM Loan, Member where Loan.memberId=Member.memberId AND Member.username LIKE '%%%s%%'";
				char statement3[1024];
				printf("%s connected....\n",user3);
				sprintf(statement3,query3,user3);
				
	//querying the select statement
				mysql_query(conn,statement3);
					res3 = mysql_store_result(conn);
					int counter3=0;
				
		//fetching from the database
					while((row3 = mysql_fetch_row(res3))!=NULL)
					{
					if(counter3>1) continue;
					printf("%s\n",row3[0]);
					char *hold3;
					hold3=row3[0];// initializing the result from the database to the variable hold3
					if(hold3==NULL){
								char result3[100]="Result not found\n";
								send(client_socket, result3, sizeof(result3), 0);
								printf("Client reply sent successfully\n");
					}else{
						strcat(hold3,":/-\n");

			//sending the result back to the client
					 	send(client_socket, hold3, sizeof(hold3), 0);
						printf("Client reply sent successfully\n");
						counter3++;
					}
					}
				
					mysql_free_result(res3);
					mysql_close(conn);//closing the mysql database connection
				}

	//fetching the loan repayment details of a particular user
			}else if(strncmp(sent,"loan repayment_details",22)==0){
			MYSQL_RES *res4;
			MYSQL_ROW row4;
			MYSQL *conn;
		       conn= mysql_init(NULL);

	//connection to the mysql database
			if(!(mysql_real_connect(conn,host,user,password,dbname,port,NULL,0))){
					fprintf(stderr,"\n %s [%d]\n",mysql_error(conn),mysql_errno(conn));
					exit(1);
				}else{

	//splitting the string sent from the client
			char * pch4;
			char *user4;
 			pch4 = strtok (sent," ");
			int count4=0;
			while (pch4 != NULL)
  			{
			if(count4==2){
			printf ("%s\n",pch4);
			user4=pch4;
			} 
    
			pch4 = strtok (NULL, " ");
			count4++;
			}
			printf("connection to the database successful\n");
			
	//initializing the select query
				char query4[]="SELECT sum(Loan_repayment.amountPaid) FROM Loan_repayment, Member where Loan_repayment.memberId=Member.memberId AND Member.username LIKE '%%%s%%'";

				char statement4[1024];
				printf("%s connected....\n",user4);
				sprintf(statement4,query4,user4);
	
	//querying the select statement
				mysql_query(conn,statement4);
					res4 = mysql_store_result(conn);
					int counter4=0;
				
		//fetching from the database
					while((row4 = mysql_fetch_row(res4))!=NULL)
					{
					if(counter4>1) continue;
					printf("%s\n",row4[0]);
					char *hold4;
					hold4=row4[0];// initializing the result from the database to the variable hold4
					if(hold4==NULL){
									char result4[100]="Result not found\n";
									send(client_socket, result4, sizeof(result4), 0);
									printf("Client reply sent successfully\n");
						}else{
						strcat(hold4,":/-\n");

			//sending the result back to the client
					 	send(client_socket, hold4, sizeof(hold4), 0);
						bzero(hold4,sizeof(hold4));
						bzero(sent,sizeof(sent));
						printf("Client reply sent successfully\n");
						counter4++;
						}
						}
					mysql_free_result(res4);
					mysql_close(conn);//closing the mysql database connection
				
				}
			}else{

	//writing to the file 
				printf("Client message recieved successfully\n");
				records = fopen("/opt/lampp/htdocs/project/records.txt", "a"); //opening the file records.txt
				fprintf(records,"%s\n",sent);
				fclose(records);
	//sending reply to the client
				if(send(client_socket, sub, sizeof(sub), 0)==0){
					printf("Client reply sent successfully\n");
	
			}
		}
	}
	close(client_socket);
 }
	close(serv_socket);
	return 0;
}

