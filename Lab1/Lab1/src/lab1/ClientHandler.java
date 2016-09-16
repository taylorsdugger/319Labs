package lab1;

import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.Socket;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.Arrays;
import java.util.Scanner;

public class ClientHandler implements Runnable {
		Socket s; // this is socket on the server side that connects to the CLIENT
		int num; // keeps track of its number just for identifying purposes
		String name;

		ClientHandler(Socket s, int n, String name) {
			this.s = s;
			num = n;
			this.name = name;
		}

		// This is the client handling code
		// This keeps running handling client requests 
		// after initially sending some stuff to the client
		public void run() { 
			Scanner in;
			PrintWriter out;
			
			try {
				// 1. GET SOCKET IN/OUT STREAMS
				in = new Scanner(new BufferedInputStream(s.getInputStream())); 
				out = new PrintWriter(new BufferedOutputStream(s.getOutputStream()));
		
				// 3. KEEP LISTENING AND RESPONDING TO CLIENT REQUESTS
				String message = "";
				String cmd = "";
				
				while (!cmd.equals("leave")) {
					message = in.nextLine();
					byte b = 0;
					
					// set our byte bit by bit
					b = (byte) (b | (1 << 7)); // 1
					b = (byte) (b | (1 << 6)); // 1
					b = (byte) (b | (1 << 5)); // 1
					b = (byte) (b | (1 << 4)); // 1
					b = (byte) (b & ~(1 << 3));// 0
					b = (byte) (b & ~(1 << 2));// 0
					b = (byte) (b & ~(1 << 1));// 0
					b = (byte) (b & ~(1 << 0));// 0
					
					message = Encryption.decode(message, b);
					
					Scanner s = new Scanner(message);
					cmd = s.next();

					if(cmd.equals("print")){
						handleRequest(s.nextLine().trim());
					}else if(cmd.equals("iÏage")){
						String image_string = s.nextLine();
						String decoded = get_file_decoded_string(image_string);
						System.out.println(name + " sent an image: " + decoded);
					}else if(cmd.equals("broadcast")){
						message = s.nextLine().trim();
						Server.broadcast(message);
					}
				}
				
				if(cmd.equals("leave")){
					System.out.println(name + " has left the chatroom.");
				}
				
			} catch (IOException e) {
				e.printStackTrace();
			}
			
			// This handling code dies after doing all the printing
		} // end of method run()
		
		void handleRequest(String s) {
			 // out and in are client side input/output streams
			System.out.println(name + ": " + s);
			
			// create file chat.txt and store messages in it
			
		}
		
		public String get_file_decoded_string(String encoded_string) throws IOException{
			
			String bin_string = "";
			String final_bin_string = "";
			String s;
			int i;
			int count = 0;
			byte[] bytes = encoded_string.getBytes();
			
			for(i = 0; i < bytes.length; i++){
				bin_string = String.format("%8s", Integer.toBinaryString(bytes[i] & 0xFF)).replace(' ', '0');
				final_bin_string += bin_string.substring(2, 8);
			}
			
			byte[] new_bytes = new byte[final_bin_string.length() % 8];
			
			for(i = 0; i < final_bin_string.length(); i = i+8){
				if(i+8 < final_bin_string.length()){
					s = final_bin_string.substring(i, i+8);
					byte converted = (byte) Integer.parseInt(s);
					new_bytes[count++] = converted;
				}
			}
			
			String decoded_string = new String(new_bytes);
			
			return decoded_string;
		}// end of function get_file_decoded_string()

	} // end of class ClientHandler
