import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;

public class TRequest {

	public static void main(String[] params) throws Exception {

		String path = params.length > 0 ? params[0] : null;
		if (path == null || path.trim().length() == 0) {
			path = "t.php";
		}
		path = "http://postupayu.ru" + (path.startsWith("/") ? "" : "/") + path;

		TRequest test = new TRequest();
		long t = System.currentTimeMillis();
		test.start(path);
		t = System.currentTimeMillis() - t;
		System.out.println("Execution time: " + ((double) t / 1000)
				+ " seconds");
	}

	private void start(String url) throws Exception {
		System.out.println("Sending request to: " + url);

		URL serverAddress = new URL(url);
		HttpURLConnection connection = (HttpURLConnection) serverAddress
				.openConnection();
		connection.setRequestMethod("GET");
		connection.setDoOutput(true);

		connection.connect();

		BufferedReader rd = new BufferedReader(new InputStreamReader(
				connection.getInputStream(), "UTF-8"));

		StringBuilder sb = new StringBuilder("");
		String line;
		while ((line = rd.readLine()) != null) {
			sb.append(line);
		}

		rd.close();
		connection.disconnect();

		System.out.println("Response received:");
		System.out.println("<<<START>>>");
		System.out.println(sb.toString());
		System.out.println("<<<END>>>");
	}
}
