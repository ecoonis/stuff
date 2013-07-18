import time
import sys
import urllib.request
from optparse import OptionParser


class SiteChecker:

	logFile = None
	options = None

	def parse(self, args):
		parser = OptionParser()
		
		parser.add_option("-f", "--file", dest="filename",
			help="Write report to FILE", metavar="FILE", default=None)
		parser.add_option("-i", "--interval", dest="interval",
			help="Check interval in seconds", default=10, type="int")
		parser.add_option("-u", "--url", dest="url",
			help="URLs to check", type="string")

		(options, args) = parser.parse_args(args)

		if not options.url:
			parser.error('URLs not given')

		self.options = options

		self.start()

	def start(self):
		while(1):
			self.check()
			time.sleep(int(self.options.interval))

	def check(self):

		start = time.time()
		response = urllib.request.urlopen(self.options.url)
		duration = time.time()-start

		if(response.status == 200):

			line = '{0};{1};{2};{3};{4}'.format(
				self.options.url,
				int(time.time()),
				response.status,
				len(response.read()),
				round(duration, 2))
			
			if(self.options.filename):
				self.logFile = open(self.options.filename, 'a')
				self.logFile.write(line + '\n')
				self.logFile.close()
			else:
				print(line)
					
checker = SiteChecker()
checker.parse(sys.argv)







