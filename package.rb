#!/bin/ruby
# The packaging script

require 'fileutils'

cur  = Dir.pwd
temp = "/tmp/fl-package"
root = File.join(temp, "feedlibrary")

# Create a temp folder
def reset(folder)
  FileUtils.rmtree(folder) if File.exists?(folder)
  FileUtils.mkdir(folder)
end

# Removes CVS folders from a given folder recursively
def remove_cvs(folder)
  Dir.foreach(folder) do |e|
    next if /^\.+$/ =~ e
    
    full_path = File.join(folder, e)
    if e == "CVS"
      FileUtils.rmtree(full_path)
    elsif /^\.[^h]/ =~ e
      FileUtils.rm(full_path)
    elsif File.directory?(full_path)
      remove_cvs(full_path)
    end
  end
end

# Copy contents to temp
puts "Copying files..."
reset(temp)
FileUtils.cp_r(".", root)

puts "Removing CVS data..."
remove_cvs(temp)

puts "Removing files..."
[ "package.rb", "docs/code-license.txt" ].each do |name|
  puts " - #{name}"
  FileUtils.rm(File.join(root, name))
end

puts "Removing custom sites..."
sites = File.join(root, "sites")
Dir.foreach(sites) do |name|
  next if /^\.+/ =~ name

  full_path = File.join(sites, name)
  if File.directory?(full_path) && name != "default"
    puts " - #{name}"
    FileUtils.rmtree(full_path) 
  end
end

puts "Removing update scripts..."
FileUtils.rm Dir.glob(File.join(root, "sql", "update*"))

puts "Removing tests..."
FileUtils.rmtree File.join(root, "tests")

puts "Removing Amazon keys..."
config_file = File.join(root, "sites", "config.php")
config = open(config_file).read
config.gsub!(/'AMAZON_AWSACCESSKEYID', '[^']+'/, "'AMAZON_AWSACCESSKEYID', ''")
config.gsub!(/'AMAZON_SECRET_ACCESSKEY', '[^']+'/, "'AMAZON_SECRET_ACCESSKEY', ''")
open(config_file, "w") do |f|
  f.write(config)
end

puts "Moving documentation"
[ ["installation.txt", "INSTALLATION.txt"], ["faq.txt", "FAQ.txt"] ].each do |src, dst|
  FileUtils.mv(File.join(root, "docs", src), File.join(temp, dst))
end
FileUtils.cp(File.join(root, "license.html"), File.join(temp, "LICENSE.html"))
FileUtils.rmtree(File.join(root, "docs"))

puts "Packaging..."
version_file = File.join(root, "version.php")
contents = open(version_file).read
version = contents.scan(/'FL_VERSION',\s+'([^']+)'/) #'
package_file = File.join(Dir.pwd, "..", "feedlibrary-#{version}.tar.bz2")
puts " - Version: #{version} (#{package_file})"
`cd #{temp}; tar jcf #{package_file} .` 

puts "Removing temp..."
FileUtils.rmtree(temp)
