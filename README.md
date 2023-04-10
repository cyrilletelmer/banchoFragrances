# banchoFragrances
API for helping building perfumes based on ingredients


Aim of this API is to assist a perfume creator hobbyist by suggesting ingredients based on statistical analysis of market perfumes compositions.

This API only has 3 calls, all GET calls.
- GetINgredients: getting possible ingredients
- GetSmell : analyzing a perfume recipe
- GetSuggestions : getting suggestions to complete a perfume recipe.

## GetIngredients: Getting ingredients
This call allows to browse through ingredients present in database
Get Ingredients:

GET /ingredients/[id]/?[note_type={BASE|MIDDLE|TOP}]

Answer: 
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

## GetSuggestions : getting suggestions of ingredients
This call will propose additional ingredients


