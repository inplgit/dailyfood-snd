<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDF;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class GenerateProductPdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:product-pdf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the product pdf';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::channel('product')->info('Starting the product command.');

        $products = Product::all();


        $pdf = PDF::loadView('pages.Products.Product.PdfProductList', compact('products'));

        $pdfPath = storage_path('app/public/product_pdf_list.pdf');
        if (file_exists($pdfPath)) {
            unlink($pdfPath); // Delete the existing file
        }
        // $pdf->save($pdfPath);
        file_put_contents($pdfPath, $pdf->output());

        \Log::channel('product')->info('Starting the product command.');

        // $pdfUrl = asset('storage/app/public/multi_table_report.pdf');
        // echo $pdfUrl;

        // $fileName = 'product_pdf_list.pdf';
        // $pdfPath = 'public/' . $fileName;

        // // Check if the file exists and delete it
        // if (Storage::disk('public')->exists($fileName)) {
        //     Storage::disk('public')->delete($fileName);
        // }

        // // Save the new PDF to the 'public' disk
        // Storage::disk('public')->put($fileName, $pdf->output());

        // return 0;
    }
}
