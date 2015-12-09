import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.Comparator;
import java.util.Date;
import java.util.HashSet;
import java.util.Iterator;
import java.util.Map;
import java.util.Set;
import java.util.SortedMap;
import java.util.TreeMap;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class SpeedTest {
	private static final String TEST_URL = "postupayu.ru";
	private static final String LINKS_SOURCE_URL = "map.php";
	// private static final String TEST_URL = "www.gazeta.ru";
	// private static final String LINKS_SOURCE_URL = "";

	private static final boolean PHP_ONLY = true;
	private static final int REQUESTS_COUNT = 5;

	private static final SimpleDateFormat sdf = new SimpleDateFormat(
			"dd.MM.yyyy HH.mm.ss");

	private static final DecimalFormat df = new DecimalFormat("#.##");

	public static void main(String[] params) throws Exception {
		SpeedTest test = new SpeedTest();
		test.start();
	}

	private static class TestRes {
		String html;
		String url;

		public TestRes(String html, String url) {
			this.html = html;
			this.url = url;
		}
	}

	private void start() throws Exception {
		Urls urls = getUrls();

		int urlNum = 0;
		int urlTotal = urls.valid.size();

		SortedMap<Integer, TestRes> result = new TreeMap<Integer, TestRes>(
				new Comparator<Integer>() {
					@Override
					public int compare(Integer o1, Integer o2) {
						return o1 < o2 ? 1 : -1;
					}
				});

		for (String url : urls.valid) {
			String html = null;

			long lastTime = 0;
			long totalTime = 0;
			int requestsNum = 0;

			String msg = ++urlNum + " of " + urlTotal;

			System.out.println("================= " + msg
					+ " ==================");
			System.out.println(url);

			for (int i = 0; i <= REQUESTS_COUNT; ++i) {
				lastTime = System.currentTimeMillis();
				try {
					html = this.getUrlData(url);
				} catch (Exception ex) {
					html = null;
					System.err.println("Cant open url: " + url + ", "
							+ ex.getMessage());
					break;
				}
				lastTime = System.currentTimeMillis() - lastTime;

				if (i == 0) {
					// "Нулевой" запрос, делаем из-за кеширования
					System.out.println(i + "|" + lastTime + "|zero");
				} else {
					++requestsNum;
					totalTime += lastTime;
					System.out.println(i + "|" + lastTime + "|" + totalTime);
				}
			}

			if (html != null) {
				int averege = (int) Math.round(((double) totalTime)
						/ requestsNum);
				System.out.println("-------------");
				System.out.println(requestsNum + "|" + averege + "|"
						+ totalTime);
				System.out.println();

				result.put(averege, new TestRes(html, url));
			}
		}

		String date = sdf.format(new Date());

		StringBuilder report = new StringBuilder();
		report.append(this.getRepCss());
		report.append("<h3>Отчёт за " + date + " по сайту " + TEST_URL
				+ "</h3>");
		report.append("<p>Количество запросов для набора статистики: "
				+ REQUESTS_COUNT + "</p>");
		report.append("<table>");

		Set<Map.Entry<Integer, TestRes>> datas = result.entrySet();
		int num = 0;

		for (Iterator<Map.Entry<Integer, TestRes>> iterator = datas.iterator(); iterator
				.hasNext();) {
			Map.Entry<Integer, TestRes> entry = iterator.next();
			String href = ensureHttpUrl(entry.getValue().url);
			String sec = df.format(((double) entry.getKey()) / 1000);
			String hrefVal = "<a href='" + href + "' target='_blank'>" + href
					+ "</a>";
			String html = entry.getValue().html;
			String title = getPageTitle(html);

			report.append("<tr>");
			report.append("<td>").append(++num).append(".</td>");
			report.append("<td>").append(hrefVal).append("</td>");
			report.append("<td>[").append(title).append("]</td>");
			report.append("<td>").append(sec).append(" сек</td>");
			report.append("<td>").append(html.length())
					.append(" символов</td>");
			report.append("</tr>");
		}
		report.append("</table>");

		report.append("<h4>Пропущенные ссылки:</h4>");

		report.append("<table>");
		for (String invaidUrl : urls.invalid) {
			String href = ensureHttpUrl(invaidUrl);
			String hrefVal = "<a href='" + href + "' target='_blank'>" + href
					+ "</a>";
			report.append("<tr><td>").append(hrefVal).append("</td></tr>");
		}
		report.append("</table>");

		String fileName = saveReport(report.toString(), date);
		System.out.println("Report is ready: " + fileName);
	}

	private String getRepCss() {
		StringBuilder css = new StringBuilder();
		css.append("<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />");
		css.append("<style>");
		css.append("td{padding-right: 10px}");
		css.append("</style>");
		return css.toString();
	}

	private String ensureHttpUrl(String url) {
		return url.startsWith("http:") ? url : "http://" + TEST_URL
				+ (url.startsWith("/") ? "" : "/") + url;
	}

	private static class Urls {
		public Set<String> valid;
		public Set<String> invalid;

		public Urls(Set<String> valid, Set<String> invalid) {
			this.valid = valid;
			this.invalid = invalid;
		}
	}

	private Urls getUrls() throws IOException {
		String content = getUrlData(LINKS_SOURCE_URL);

		/*
		 * Вычислим путь относительных ссылок. /x/y/z.php - нужно извлечь /x/y/
		 */
		String relLinkPrefix = LINKS_SOURCE_URL;
		int i = relLinkPrefix.lastIndexOf("/");
		if (i > 0) {
			relLinkPrefix = relLinkPrefix.substring(0, i + 1);
		} else {
			relLinkPrefix = "";
		}

		content = content.replace("\"", "'");

		Pattern p = Pattern.compile("href='([^']*)'");
		Matcher matcher = p.matcher(content);

		Set<String> valid = new HashSet<String>();
		Set<String> invalid = new HashSet<String>();
		while (matcher.find()) {
			String url = matcher.group(1);
			url = url == null ? "" : url.trim();
			if (url.length() == 0//
					|| (PHP_ONLY && !url.contains(".php"))//
					|| url.contains("#")) {

				if (!invalid.contains(url)) {
					System.out.println(" - " + url);
				}
				invalid.add(url);
				continue;
			}

			String actualUrl = url;

			if (!actualUrl.startsWith("http") && !actualUrl.startsWith("/")) {
				actualUrl = relLinkPrefix + actualUrl;
			}

			if (!valid.contains(url)) {
				if (url.equals(actualUrl)) {
					System.out.println(" + " + url);
				} else {
					System.out.println(" + " + url + " [" + actualUrl + "]");
				}
			}

			valid.add(actualUrl);
		}

		return new Urls(valid, invalid);
	}

	private static final Pattern p = Pattern.compile("<title>([^<]*)</title>");

	private String getPageTitle(String html) {
		Matcher matcher = p.matcher(html);

		String title = null;
		if (matcher.find()) {
			title = matcher.group(1);
		}
		return title == null ? "" : title.trim();
	}

	private String getUrlData(String url) throws IOException {
		url = ensureHttpUrl(url);

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

		return sb.toString();
	}

	private String saveReport(String report, String date) throws Exception {

		String pstoolsPath = new File("test").getAbsoluteFile().getParentFile()
				.getParent();

		String reportsDirPath = pstoolsPath + "/SpeedReports/";

		File file = new File(reportsDirPath);
		if (!file.exists()) {
			file.mkdirs();
		}

		String fileName = TEST_URL + "_" + date + ".html";

		saveStringIntoFile(reportsDirPath, fileName, report);
		saveStringIntoFile(reportsDirPath, "last.html", report);

		return fileName;
	}

	private void saveStringIntoFile(String dirPath, String fileName, String data)
			throws Exception {
		String filePath = dirPath + fileName;

		BufferedOutputStream stream = new BufferedOutputStream(
				new FileOutputStream(new File(filePath), false));
		try {
			stream.write(data.getBytes("UTF-8"));
			stream.flush();
		} finally {
			stream.close();
		}
	}
}
