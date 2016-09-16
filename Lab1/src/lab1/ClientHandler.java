package lab1;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.net.Socket;
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
			
			try {
				// 1. GET SOCKET IN/OUT STREAMS
				in = new Scanner(new BufferedInputStream(s.getInputStream())); 
		
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
					message = message.replace('Ï', 'm');
					
					Scanner s = new Scanner(message);
					cmd = s.next();

					if(cmd.equals("print")){
						handleRequest(s.nextLine().trim());
					}else if(cmd.equals("image")){
						String image_string = s.nextLine();
						System.out.println(image_string);
						String decoded = get_file_decoded_string(image_string);
						System.out.println(name + " sent an image: " + decoded);
					}else if(cmd.equals("broadcast")){
						message = s.nextLine().trim();
						Server.broadcast(message);
					}
					s.close();
				}
				
				if(cmd.equals("leave")){
					System.out.println(name + " has left the chatroom.");
				}
				
			} catch (IOException e) {
				e.printStackTrace();
			}
			
			// This handling code dies after doing all the printing
		} // end of method run()
		
		void handleRequest(String s) throws IOException {
			 // out and in are client side input/output streams
			String out = name + ": " + s;
			System.out.println(out);
			write_chat_history(out, -1);
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
			
			byte[] new_bytes = new byte[final_bin_string.length()];
			
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
		
		public static int write_chat_history(String s, int line_num) throws IOException{
			String line;
			String filename = "chat.txt";
			File f = null;
			
			f = new File(filename);
			
			if(f.exists()){
				// file exists, create a copy
				File source_file = new File(filename);
	            File copy_file = new File("chat_copy.txt");
	            copyFileUsingStream(source_file, copy_file);
	            
	            FileReader fileReader = new FileReader("chat_copy.txt");

	            // Always wrap FileReader in BufferedReader.
	            BufferedReader bufferedReader = new BufferedReader(fileReader);
	            
	            try{
	            	
			         // create new file
			         f = new File(filename);
			         
			         // tries to create new file in the system
			         f.createNewFile();
			         
			         PrintWriter writer = new PrintWriter(filename, "UTF-8");
			         int line_count = 0;
			         
			         while((line = bufferedReader.readLine()) != null) {
			        	 if(line_num >= 0 && line_count == line_num){
			        		 line_count++;
			        		 continue;
			        	 }else{
			        	     line_count++;
			        	 }
		                writer.println(line);
			         }

			         if(line_count <= line_num){
			        	 writer.close();
			        	 return -2;
			         }
			         
			         if(line_num == -1){
			        	 writer.println(s);
			         }
			         
			         // clean up
			         writer.close();
			         bufferedReader.close();
			         fileReader.close();
			         copy_file.delete(); 
			      }catch(Exception e){
			         e.printStackTrace();
			      }       
	            
	            // Always close files.
	            bufferedReader.close(); 
			}else{
				if(line_num >= 0){
					return -1;
				}
				// file doesnt exist, create it now
				try{
			         // create new file
			         f = new File("chat.txt");
			         
			         // tries to create new file in the system
			         f.createNewFile();
			         
			         PrintWriter writer = new PrintWriter("chat.txt", "UTF-8");
			         writer.println(s);
			         writer.close();   
			      }catch(Exception e){
			         e.printStackTrace();
			      } 
			}// end if file exists
			return 0;
		}// end function write_chat_history()
		
		private static void copyFileUsingStream(File source, File dest) throws IOException {
		    InputStream is = null;
		    OutputStream os = null;
		    try {
		        is = new FileInputStream(source);
		        os = new FileOutputStream(dest);
		        byte[] buffer = new byte[1024];
		        int length;
		        while ((length = is.read(buffer)) > 0) {
		            os.write(buffer, 0, length);
		        }
		    } finally {
		        is.close();
		        os.close();
		    }
		}

	} // end of class ClientHandler
