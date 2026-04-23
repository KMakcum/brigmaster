# Brigmaster Drywall Calculator

## Goal

Turn the drywall estimator into a practical assistant for:

- wall cladding on a metal frame
- suspended ceilings
- drywall partitions

The calculator is designed as a material planning tool, not as a substitute for a full working design.

## Supported modes

- `dimensions` - calculates sheets, profiles and fasteners from geometry
- `area` - calculates sheets and finishing materials from total area only

In `area` mode the calculator does not estimate frame profiles or frame-related hardware because the construction geometry is unknown.

## Supported targets

- `wall`
- `ceiling`
- `partition`

## Inputs

### Common

- drywall target
- sheet size in mm
- sheet thickness in mm
- number of layers
- frame step in mm
- reserve for sheets and profiles
- reserve for fasteners

### Geometry

#### Wall

- total wall length
- height

Hint in UI: for a room, the user can sum all wall lengths and enter one total value.

#### Ceiling

- room length
- room width

#### Partition

- partition length
- height
- profile width: 50 / 75 / 100 mm

### Openings

For walls and partitions:

- windows
- doors
- width / height / count

For partitions there is an extra toggle:

- include cladding on opening edges

This adds drywall strips for jambs and the top of the opening based on the calculated partition thickness.

### Finishing

Optional section:

- primer
- joint putty
- finish putty
- reinforcing tape

### Costs

Optional section with partial pricing support:

- sheet price per unit
- profile price per meter
- fastener price per 100 pcs
- primer price per kg
- joint putty price per kg
- finish putty price per kg
- tape price per meter

If only part of prices is filled, only those rows are shown in the result.

## Calculation model

### Sheets

- gross area comes from geometry or direct area input
- openings are subtracted for walls and partitions
- for partitions, the cladding area is multiplied by two sides
- the layer multiplier is applied to all board-facing surfaces
- optional opening edge cladding is added for partitions
- reserve is applied after net sheet area is calculated

Results:

- exact sheet count
- sheet count with reserve
- purchase count rounded up to full sheets

### Profiles

#### Wall

- guide profile: top + bottom
- main profile: vertical members by frame step
- cross profile: horizontal rows for sheet joints above one sheet length

#### Ceiling

- guide profile: perimeter
- main profile: rows by frame step
- cross profile: rows for sheet joints along room length
- direct hangers: by main profile rows and suspension step
- crab connectors: by cross intersections

#### Partition

- guide profile: top + bottom
- main profile: studs by frame step
- cross profile: sheet-joint rows and framing around openings

Results show:

- calculated running meters
- rounded running meters for purchase

The UI intentionally does not bind purchase guidance to a fixed commercial bar length, because actual supply lengths may differ.

### Fasteners and hardware

The calculator shows both:

- base quantity
- quantity with reserve

Reserve is controlled by a dedicated fastener reserve field.

Included positions:

- drywall screws
- profile screws
- dowel nails
- direct hangers
- crab connectors

In `area` mode only sheet-related screws remain available as an area-based estimate; frame-dependent items are omitted.

### Finishing

Reference norms used in the calculator:

- primer: `0.1 kg/m2`
- joint putty: `0.4 kg/m2`
- finish putty: `1.2 kg/m2`
- reinforcing tape: `1.2 lm/m2`

These are intentionally presented to the user as an ориентировочный расход.

## Result structure

- geometry
- drywall sheets
- profiles
- fasteners and hardware
- finishing materials
- costs
- notes

## Important notes

- The calculator assumes a rectangular construction geometry.
- It is intended for planning and procurement, not for engineering verification.
- Normative references and public-facing methodology text should be finalized in page content after UX and formulas are approved by the user.
