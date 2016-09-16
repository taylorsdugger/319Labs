package lab1;

import java.io.BufferedOutputStream;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.ArrayList;
import java.util.Scanner;

public class Server implements Runnable {
	
	static ArrayList<Socket> clients = new ArrayList<>();
	ServerSocket serverSocket = null;
	int clientNum = 0; // keeps track of how many clients were created
	
	public void run(){
		// 1. CREATE A NEW SERVERSOCKET
		try {
			serverSocket = new ServerSocket(4444); // provide MYSERVICE at port 
													// 4444
			System.out.println("Chatroom has been started");
		} catch (IOException e) {
			System.out.println("Could not listen on port: 4444");
			System.exit(-1);
		}

		// 2. LOOP FOREVER - SERVER IS ALWAYS WAITING TO PROVIDE SERVICE!
		while (true) { // 3.
			Socket clientSocket = null;
			try {

				// 2.1 WAIT FOR CLIENT TO TRY TO CONNECT TO SERVER
				clientSocket = serverSocket.accept(); // // 4.
				clients.add(clientSocket);
				
				Scanner in = new Scanner(clientSocket.getInputStream());
				String name = in.nextLine();
				
				// 2.2 SPAWN A THREAD TO HANDLE CLIENT REQUEST
				System.out.println(name + " joined the chatroom.");
				
				Thread t = new Thread(new ClientHandler(clientSocket, clientNum, name));
				t.start();
				
			} catch (IOException e) {
				System.out.println("Accept failed: 4444");
				System.exit(-1);
			}

			// 2.3 GO BACK TO WAITING FOR OTHER CLIENTS
			// (While the thread that was created handles the connected client's
			// request)

		} // end while loop
		
	}// end of run method
	
	public static void main(String args[]) {
        new Thread(new Server()).start();
    }
	
	public static void broadcast(String message) throws IOException{
		PrintWriter out;
		
		for (int i = 0; i < clients.size(); i++) {
			out = new PrintWriter(new BufferedOutputStream(clients.get(i).getOutputStream()));
			out.println("broadcast " + message.trim());
			out.flush();
		}
	}
}// end of server class
