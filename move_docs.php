<?php
$date = date('d/m/Y');
$source_dir = 'C:/Users/DELL/.gemini/antigravity/brain/c1e6fe8c-f136-48d7-996c-a3b6b5a300fc';
$dest_dir = 'E:/projects/pk401/pk40/docs';

$files = ['implementation_plan.md', 'task.md', 'walkthrough.md', 'cap_nhat-csdl.md'];

foreach ($files as $file) {
    $src = "$source_dir/$file";
    $dst = "$dest_dir/$file";
    
    if (file_exists($src)) {
        $content = file_get_contents($src);
        $new_content = "# Ngày " . $date . "\n\n" . $content . "\n\n---\n\n";
        
        if (file_exists($dst)) {
            $old_content = file_get_contents($dst);
            file_put_contents($dst, $new_content . $old_content);
        } else {
            file_put_contents($dst, $new_content);
        }
    }
}
echo "Done moving artifacts to docs.\n";
