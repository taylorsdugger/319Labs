package lab1;

import java.io.IOException;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.Scanner;

public class Server {

	public static void main(String[] args) throws IOException {

		ServerSocket serverSocket = null;
		String clientName = null;

		try {
			serverSocket = new ServerSocket(4444); 
													
			System.out.println(serverSocket);
		} catch (IOException e) {
			System.out.println("Could not listen on port: 4444");
			System.exit(-1);
		}

		while (true) {
			Socket clientSocket = null;
			try {
				clientSocket = serverSocket.accept();

				Scanner in = new Scanner(clientSocket.getInputStream());
				clientName = in.nextLine();
				in.close();
				System.out.println("Server got connected to a client " + clientName);
				
				//Thread t = new Thread(new ClientHandler(clientSocket, clientName));
				//t.start();

			} catch (IOException e) {
				System.out.println("Accept failed: 4444");
				System.exit(-1);
			}
		}
	}
} 
