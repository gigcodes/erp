<?php


namespace App\Services\Listing;


use App\AttributeReplacement;
use App\Brand;
use App\Product;
use App\Services\Grammar\GrammarBot;

class ShortDescriptionChecker implements CheckerInterface
{

    private $grammerBot;

    public function __construct(GrammarBot $bot)
    {
        $this->grammerBot = $bot;
    }

    public function check($product): bool {
        $data = $product->short_description;
        if (strlen($data) < 60) {
            return false;
        }
        $data = $this->improvise($data);
        $product->short_description = $data;
        $product->save();
        $state = $this->grammerBot->validate($data);

        if ($state !== false) {
            $product->short_description = $state;
            $product->save();

            return true;
        }

        return false;

    }

    public function improvise($sentence, $data2 = null): string
    {

        //Remove words that needs to be removed...
        $sentence = strtolower($sentence);
        $replacements = AttributeReplacement::where('field_identifier', 'short_description')->get();
        foreach ($replacements as $replacement) {
            $sentence = str_replace(strtolower($replacement->first_term), $replacement->replacement_term, $sentence);
        }

        //Now remove special characters..
        $characters = array (
            "\n",
            '\n',
            '&excl;',
            '&quot;',
            '&num;',
            '&dollar;',
            '&percnt;',
            '&amp;',
            '&apos;',
            '&lpar;',
            '&rpar;',
            '&ast;',
            '&plus;',
            '&comma;',
            '&sol;',
            '&colon;',
            '&semi;',
            '&lt;',
            '&equals;',
            '&gt;',
            '&quest;',
            '&commat;',
            '&lbrack;',
            '&bsol;',
            '&rsqb;',
            '&Hat;',
            '&hat;',
            '&lowbar;',
            '&grave;',
            '&lbrace;',
            '&vert;',
            '&rcub;',
            '&sect;',
            '&copy;',
            '&para;',
            '\\',
            '/',
            '-'
        )
        ;

        $sentence = strtolower($sentence);

        $sentence = str_replace($characters, ' ', $sentence);

        return title_case($sentence);
    }
}