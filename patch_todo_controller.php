<?php
$file = '/Applications/XAMPP/xamppfiles/htdocs/newbizzcrm_v3/backend/app/Http/Controllers/Employee/EmployeeFrontendController.php';
$content = file_get_contents($file);

$search = <<<'CODE'
            if ($request->filled('date_range')) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) == 2) {
                    try {
                        $start = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                        $end = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                        $query->whereBetween('due_date', [$start, $end]);
                    } catch (\Exception $e) {
                        // ignore invalid dates
                    }
                }
            }

            if ($request->filled('sort')) {
CODE;

$replace = <<<'CODE'
            if ($request->filled('date_range')) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) == 2) {
                    try {
                        $start = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                        $end = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                        $query->whereBetween('due_date', [$start, $end]);
                    } catch (\Exception $e) {
                        // ignore invalid dates
                    }
                }
            }
            
            if ($request->filled('title')) {
                $query->where('title', 'like', '%' . $request->title . '%');
            }
            
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('priority') && $request->priority !== 'all') {
                $query->where('priority', $request->priority);
            }
            
            if ($request->filled('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            if ($request->filled('sort')) {
CODE;

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done";
