# banchoFragrances
API for helping building perfumes based on ingredients


Aim of this API is to assist a perfume creator hobbyist by suggesting ingredients based on statistical analysis of market perfumes compositions.

This API only has 3 calls, all GET calls.
- GetINgredients: getting possible ingredients
- GetSmell : analyzing a perfume recipe
- GetSuggestions : getting suggestions to complete a perfume recipe.

Every call trigger a response with this format:
{
"errorCode" : INT,
"message" : STR,
"data" : JSON
}

the JSON in Data is what will vary depeding on the call we are using



## GetIngredients: Getting ingredients
This call allows to browse through ingredients present in database


Get Ingredients:

GET /ingredients/[id]/?[note_type={BASE|MIDDLE|TOP}]

Answer (JSON "data" field) : 

JSONARRAYOF(
{
"id":INT,
"noteType":STR,
"name":JSONARAYOF( {"fr": STR, "en":STR, etc ....}),
"blendingFactor": FLOAT,
"freq": FLOAT
"adjectives" : JSONARAYOF( {"fr": STR, "en":STR, etc ....})
}
)

## GetSmell : getting analysis of a smell (a recipe)
This call analyses how much a recipe is "consistent" ie similar in statistical terms to existing recipe.




GET 
/smell/?
[amounts=INTARRAY(1,2,3...)]&
[correlation_type=<BASIC|SIGNIFICATIVE|UNGENDERED>]&
[warning_strategy=STRARRAY(PYRAMIDAL_BALANCE_WARNINGS,BASIC_WARNINGS,INDIVIDUAL_DOSING_WARNINGS...)]&
[freq_min=INT]

Example: GET smell/?ingredients=16,7,12&amounts=1,4,1&warning_strategy=INDIVIDUAL_DOSING_WARNINGS,PYRAMIDAL_BALANCE_WARNINGS


Answer (JSON "data" field) : 

{
"ingredients":JSONARRAYOF(INGREDIENTS),
"averageCorrelations":JSONARRAYOF({"type":STR,"value":DOUBLE})
"warnings" : JSONARRAYOF({"type":STR(<EXCESS_AMOUNT|LACKS_BASE|LACKS_MIDDLE|LACKS_TOP>), "targetOfWarning":OPTINT})
}



## GetSuggestions : getting suggestions of ingredients


This call will propose additional ingredients



GET /suggestion/?ingredients=INTARRAY(1,2,3...)&[amounts=INTARRAY(1,2,3...)] & note_type=<BASE,MIDDLE,TOP>&[desirability_type=<BASIC,SIGNIFICATIVE,UNGENDERED>]

Answer (JSON "data" field) : 



{
"NotesSuggestions":JSONARRAYOF({"desirabilityValue":FLOAT,"ingredient":INGREDIENT,"suggestedAmount":INT})
}



# quick tool to create a fragrance using this API:

https://avezvouslesharingan.fr/perfumeUI/cli.htm
